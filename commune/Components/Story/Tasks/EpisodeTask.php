<?php


namespace Commune\Components\Story\Tasks;


use Closure;
use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Dialogue\Dialog;
use Commune\Chatbot\OOHost\Dialogue\Redirect;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Story\Basic\AbsScriptTask;
use Commune\Components\Story\Basic\EpisodeDefinition;
use Commune\Components\Story\Options\ScriptOption;

/**
 * @method EpisodeDefinition getDef(): Definition
 * @property string $currentStage
 */
class EpisodeTask extends AbsScriptTask
{
    const STAGE_TO_MENU = 'toMenu';
    const STAGE_GOOD_ENDING = 'goodEnding';
    const STAGE_BAD_ENDING = 'badEnding';
    const STAGE_UNLOCK_EPISODE = 'unlockEpisode';

    const STAGES = [
        self::STAGE_TO_MENU,
        self::STAGE_GOOD_ENDING,
        self::STAGE_BAD_ENDING,
        self::STAGE_UNLOCK_EPISODE,
    ];

    /**
     * @var string
     */
    protected $episodeName;

    public function __construct(string $scriptName, string $episodeName)
    {
        $this->episodeName = $episodeName;
        parent::__construct(
            $scriptName,
            ScriptOption::makeEpisodeName($scriptName, $episodeName)
        );
    }

    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }


    public function goMenu(): Closure
    {
        return function(Dialog $dialog) {
            return $dialog->goStage('toMenu');
        };
    }

    public function goFallback(): Closure
    {
        return function(Dialog $dialog) : Navigator {

            $dialog->say()->info($this->getScriptOption()->parseReplyId('quitEpisode'));

            return $dialog->redirect->replaceTo(
                new ScriptMenu($this->scriptName),
                Redirect::THREAD_LEVEL
            );
        };
    }

    public function __staging(Stage $stage) : void
    {
        $name = $stage->name;
        if ($name != 'toMenu') {
            $this->currentStage = $name;
        }
    }

    /*--------- stages ----------*/

    public function __onStart(Stage $stage): Navigator
    {
        $option = $this->getDef()->getEpisodeOption();
        $stages = $option->stages;
        $first = $stages[0];
        $to = $first->id;

        return $stage->dialog->goStage($to);
    }

    /**
     * Episode 跳转到菜单, 可以跳转回来.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onToMenu(Stage $stage) : Navigator
    {
        return $stage->dependOn(
            new ScriptMenu($this->scriptName),
            function(Dialog $dialog){
                return $dialog->goStage($this->currentStage);
            }
        );
    }

    /**
     * @param Stage $stage
     * @return Navigator
     */
    public function __onBadEnding(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->askConfirm($this->getScriptOption()->parseReplyId('badEnding'))
            ->hearing()
            ->isPositive(Redirector::goRestart())
            ->isNegative($this->goMenu())
            ->end();
    }

    /**
     * @param Stage $stage
     * @return Navigator
     */
    public function __onGoodEnding(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            ->info(
                $this->getScriptOption()->parseReplyId('goodEnding')
            )
            ->action(Redirector::goFulfill());

    }

    public function __onUnlockEpisode(Stage $stage) : Navigator
    {
        $unlockingEpisode = $this->mem->unlockingEpisode;
        $title = $this->getScriptOption()->getEpisodeIdToTitles()[$unlockingEpisode] ?? '';
        return $stage->buildTalk(['episode' => $title])
            ->askConfirm(
                $this->getScriptOption()->parseReplyId('startNewEpisode')
            )
            ->hearing()
            ->isPositive($this->goEpisode($unlockingEpisode))
            ->isNegative($this->goMenu())
            ->end();
    }

    /*--------- private ----------*/

    public function __sleep(): array
    {
        $names = parent::__sleep();
        $names[] = 'episodeName';
        return $names;
    }

}