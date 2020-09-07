<?php


namespace Commune\App\Contexts;


use Commune\Blueprint\Ghost\Cloner;
use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Operate\Operator;
use Commune\Blueprint\NLU\SimpleChat;
use Commune\Ghost\Context\ACodeContext;
use Commune\Ghost\IMindDef\IChatDef;
use Commune\NLU\Support\NLUUtils;
use Commune\Protocals\HostMsg\Convo\QA\AnswerMsg;
use Commune\Protocals\HostMsg\Convo\VerbalMsg;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title 闲聊功能测试
 *
 * @example 测试闲聊功能
 * @example 能和我聊天吗
 * @example 可以闲聊吗
 *
 *
 * @property string|null $say
 */
class SimpleChatContext extends ACodeContext
{
    const CHAT_GROUP = 'simple_chat_test';

    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([

            'memoryAttrs' => ['say' => null],

            'strategy' => [
                'auth' => [],
                'onCancel' => 'quit',
                'onQuit' => null,
                'heedFallbackStrategies' => [],
                'comprehendPipes' => [],
                'stageRoutes' => [],
                'contextRoutes' => [],
            ],
        ]);
    }

    public static function __depending(Depending $depending): Depending
    {
        return $depending;
    }

    public function __on_start(StageBuilder $stage): StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog){

            $service = $this->getChat($dialog->cloner);
            if (empty($service)) {
                return $dialog
                    ->send()
                    ->notice('app.simpleChat.noService')
                    ->over()
                    ->fulfill();
            }

            $mind = $dialog->cloner->mind;
            $chat = $mind->chatReg();
            $count = $chat->getMetaRegistry()->searchCount(self::CHAT_GROUP);

            return $dialog
                ->send()
                ->info('app.simpleChat.welcome')
                ->info('app.simpleChat.corpus', ['count' => $count])
                ->info('app.simpleChat.intro')
                ->over()
                ->goStage('chat');
        });
    }


    /**
     * @param StageBuilder $stage
     * @return StageBuilder
     *
     * @title 开始闲聊
     */
    public function __on_chat(StageBuilder $stage): StageBuilder
    {
        return $stage->onActivate(function (Dialog $dialog) {

            $choices = [
                'q|' => $this->getStage('quit')
            ];
            $say = $this->say;

            if (isset($say)) {
                $choices['e|'] = $this->getStage('learn');
            }

            return $dialog
                ->await()
                ->askVerbal(
                    'app.simpleChat.ask',
                    $choices
                );


        })->onReceive(function(Dialog $dialog) {

            return $dialog
                ->hearing()
                ->isAnswered()
                ->then(function(Dialog $dialog, AnswerMsg $isAnswered) {
                    $text = $isAnswered->getText();
                    return $this->hearText($dialog, $text);
                })
                ->end();
        });
    }

    /**
     * @param StageBuilder $stage
     * @return StageBuilder
     *
     * @title 退出
     */
    public function __on_quit(StageBuilder $stage): StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog) {
            return $dialog->fulfill();
        });
    }



    /**
     * @param StageBuilder $stage
     * @return StageBuilder
     *
     * @title 教我回应
     */
    public function __on_learn(StageBuilder $stage): StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog) {

            return $dialog
                ->await()
                ->withSlots(['say' => $this->say])
                ->askVerbal(
                    'app.simpleChat.teach',
                    [
                        'q|' => $this->getStage('quit'),
                        'c|' => $this->getStage('chat'),
                    ]
                );

        })->onReceive(function(Dialog $dialog) {

            return $dialog
                ->hearing()
                ->isVerbal()
                ->then(function(Dialog $dialog, VerbalMsg $isVerbal) {

                    $text = $isVerbal->getText();

                    if (empty($text)) {
                        return $dialog
                            ->send()
                            ->notice('app.simpleChat.learnEmpty')
                            ->over()
                            ->rewind();
                    }

                    $user = $dialog->cloner->input->getCreatorName();

                    $reply = "$text (by $user)";
                    $def = new IChatDef(
                        $this->say,
                        $reply,
                        self::CHAT_GROUP
                    );

                    $this->say = null;
                    $dialog->cloner->nlu->asyncSaveMeta(
                        $dialog->cloner,
                        $def->toMeta()
                    );

                    return $dialog
                        ->send()
                        ->info("app.simpleChat.learned")
                        ->over()
                        ->goStage('chat');
                })
                ->end();
        });


    }

    protected function hearText(Dialog $dialog, string $text) : Operator
    {

        if (NLUUtils::isNotNatureLanguage($text)) {
            return $dialog
                ->send()
                ->notice(
                    'app.simpleChat.notNature',
                    ['text' => $text]
                )
                ->over()
                ->goStage('chat');
        }

        $this->say = $text;

        $service = $this->getChat($dialog->cloner);

        $reply = $service->reply(
            $text,
            self::CHAT_GROUP
        );

        if (empty($reply)) {
            return $dialog->goStage('learn');
        }

        return $dialog
            ->send()
            ->info('app.simpleChat.reply', ['reply' => $reply])
            ->over()
            ->goStage('chat');
    }

    protected function getChat(Cloner $clone) : ? SimpleChat
    {
        /**
         * @var SimpleChat|null $service
         */
        $service = $clone->nlu->getService($clone, SimpleChat::class);

        return $service;
    }

}