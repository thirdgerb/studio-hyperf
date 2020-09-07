<?php


namespace Commune\App\Contexts;


use Commune\Blueprint\Ghost\Context\CodeContextOption;
use Commune\Blueprint\Ghost\Context\Depending;
use Commune\Blueprint\Ghost\Context\StageBuilder;
use Commune\Blueprint\Ghost\Dialog;
use Commune\Blueprint\Ghost\Ucl;
use Commune\Ghost\Context\ACodeContext;
use Commune\Ghost\Predefined\Memory\UserInfoMem;

/**
 * @author thirdgerb <thirdgerb@gmail.com>
 *
 * @title Demo 入口
 */
class DemoHomeContext extends ACodeContext
{
    public static function __option(): CodeContextOption
    {
        return new CodeContextOption([
            'priority' => 0,
            'strategy' => [
                'onCancel' => 'cancel',
                'stageRoutes' => ['*'],
                'contextRoutes' => ['*'],
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
            /**
             * @var UserInfoMem $userInfo
             */
            $userInfo = UserInfoMem::genUcl()->findContext($dialog->cloner);

            $fresh = $dialog->cloner->runtime->getCurrentProcess()->isFresh();
            if ($fresh) {
                $userInfo->name = $dialog->cloner->input->getCreatorName();
                $userInfo->loginTimes = $userInfo->loginTimes + 1;
            }
            $name = $userInfo->name;
            $times = $userInfo->loginTimes;

            if ($fresh) {
                $dialog = $dialog
                    ->send()
                    ->info(
                        'app.welcome',
                        ['name' => $name, 'times' => $times]
                    )
                    ->over();
            }

            if ($times > 1) {
                return $dialog->redirectTo(Ucl::make('app.markdown.kanban'));
            }

            return $dialog->goStage('intro');
        });
    }

    public function __on_intro(StageBuilder $stage) : StageBuilder
    {
        return $stage
            ->onActivate(function(Dialog $dialog){

                return $dialog
                    ->await()
                    ->askConfirm(
                        'app.ask.ifWantIntro'
                    );

            })->onReceive(function(Dialog $dialog) {

                return $dialog
                    ->hearing()
                    ->isPositive()
                    ->then($dialog->redirectTo(Ucl::make('md.demo.commune_v2_intro')))
                    ->isNegative()
                    ->then($dialog->redirectTo(Ucl::make('app.markdown.kanban')))
                    ->end();
            });
    }

    public function __on_cancel(StageBuilder $stage): StageBuilder
    {
        return $stage->onActivate(function(Dialog $dialog) {
            return $dialog
                ->send()
                ->info('app.falwell')
                ->over()
                ->quit();
        });
    }


}