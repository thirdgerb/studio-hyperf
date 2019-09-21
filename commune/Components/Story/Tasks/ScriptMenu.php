<?php


namespace Commune\Components\Story\Tasks;


use Closure;
use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\Blueprint\Message\Message;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Story\Basic\AbsScriptTask;
use Commune\Components\Story\Options\ScriptOption;

class ScriptMenu extends AbsScriptTask
{
    public function __construct(string $scriptName)
    {
        parent::__construct($scriptName, $scriptName);
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }

    public function goMenu(): Closure
    {
        return function(Dialog $dialog) : Navigator {
            return $dialog->goStage('menu');
        };
    }

    public function goFallback(): Closure
    {
        return function(Dialog $dialog) : Navigator {
            return $dialog->fulfill();
        };
    }


    /**
     * 跳转走.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onStart(Stage $stage): Navigator
    {
        if ($stage->dialog->isDepended()) {
            return $stage->dialog->goStage('menu');
        }

        $playing = $this->mem->playingEpisode;
        $target = !empty($playing) ? 'confirmPlay' : 'menu';

        return $stage->buildTalk()
            ->info(
                $this->getScriptOption()->parseReplyId('welcomeToScript'),
                [
                    'title' => $this->getScriptOption()->title
                ]
            )
            ->goStage($target);

    }

    /**
     * 跳转到菜单.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onMenu(Stage $stage) : Navigator
    {
        $script = $this->getScriptOption();
        $commands = $script->commands;

        return $stage->buildTalk()
            ->askChoose(
                $script->parseReplyId('menu'),
                [
                    $commands->selectEpisode,
                    $commands->hearDescription,
                    $commands->unlockEndings,
                    $commands->fallback,
                ]
            )
            ->hearing()

            // 选择章节
            ->isChoice(0, function(Dialog $dialog){

                return $dialog->goStage('chooseEpisode');
            })

            // 听取介绍
            ->isChoice(1, Redirector::goStage('description'))

            // 查看结局
            ->isChoice(2, Redirector::goStage('unlockEndings'))

            // 返回
            ->isChoice(3, Redirector::goFulfill())


            // 如果直接说出了章节名称.
            ->fallback($this->matchByEpisodeTitle($this->unlockEpisodes))
            ->end();
    }

    /**
     * 选择章节.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onChooseEpisode(Stage $stage) : Navigator
    {
        $scriptOption = $this->getScriptOption();
        $episodes = $this->unlockEpisodes;

        // 与用户问答.
        return $stage->talk(
        // 告知用户已解锁的章节有, 请选择.
            $this->askToChooseUnlockEpisode($scriptOption, $episodes),

            // 用户做出选择.
            $this->userChooseUnlockEpisode($scriptOption, $episodes)
        );
    }


    /**
     * 听取游戏介绍.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onDescription(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->info($this->getScriptOption()->parseReplyId('description'))
            ->goStage('menu');
    }


    /**
     * 确认开启一个章节.
     * @param Stage $stage
     * @return Navigator
     */
    public function __onConfirmPlay(Stage $stage) : Navigator
    {
        $playing = $this->mem->playingEpisode;
        $title = $this->getScriptOption()->getEpisodeIdToTitles()[$playing] ?? '';
        return $stage
            ->buildTalk([
                'episode' => $title
            ])
            ->askConfirm(
                $this->getScriptOption()->parseReplyId('confirmPlay')
            )
            ->hearing()
            ->isPositive(function(Dialog $dialog){

                return $dialog->redirect->replaceTo(
                    new EpisodeTask(
                        $this->scriptName,
                        $this->getScriptOption()
                            ->parseEpisodeId($this->mem->playingEpisode)
                    ),
                    Redirect::THREAD_LEVEL
                );

            })
            ->isNegative(Redirector::goStage('chooseEpisode'))
            ->end();

    }


    public function __onUnlockEndings(Stage $stage) : Navigator
    {
    }




    /*----------- actions -----------*/



    protected function userChooseUnlockEpisode(ScriptOption $scriptOption, array $episodes) : Closure
    {
        return function(Dialog $dialog, Message $message) use ($scriptOption, $episodes) {
            $builder = $dialog->hear($message);

            // choice 机制.
            foreach ($episodes as $index => $id) {
                $builder->isChoice($index, $this->isChoiceToPlayEpisode($id));
            }

            // fallback 机制.
            return $builder
                ->fallback($this->matchByEpisodeTitle($episodes))
                ->end();

        };
    }

    protected function matchByEpisodeTitle(array $episodeIds) : Closure
    {
        return function(Message $message, Dialog $dialog) use ($episodeIds): ? Navigator {

            $idToTitles = $this->getDef()
                ->getScriptOption()
                ->getEpisodeIdToTitles();

            $input = $message->getTrimmedText();

            foreach ($episodeIds as $episodeId) {
                $title = $idToTitles[$episodeId] ?? null;
                if (isset($title) && strstr($input, $title)) {
                    $this->mem->playingEpisode = $episodeId;
                    return $dialog->goStage('confirmPlay');
                }
            }

            return null;
        };
    }

    protected function isChoiceToPlayEpisode(string $episodeId) : Closure
    {
        return function(Dialog $dialog) use ($episodeId){
            $this->mem->playingEpisode = $episodeId;
            return $dialog->redirect->replaceTo(new EpisodeTask($this->scriptName, $episodeId));
        };
    }

    protected function askToChooseUnlockEpisode(ScriptOption $scriptOption, array $episodes) : Closure
    {
        return function(Dialog $dialog) use ($scriptOption, $episodes){

            $titles = [];
            $idToTitles = $scriptOption->getEpisodeIdToTitles();
            foreach ($episodes as $index => $episode) {
                $titles[] = $idToTitles[$episode];
            }

            $dialog->say()
                ->askChoose(
                    $scriptOption->parseReplyId('chooseEpisode'),
                    $titles
                );

            return $dialog->wait();

        };
    }







}