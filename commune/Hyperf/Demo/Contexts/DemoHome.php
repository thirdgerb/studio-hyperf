<?php


namespace Commune\Hyperf\Demo\Contexts;


use Commune\Chatbot\App\Callables\Actions\Redirector;
use Commune\Chatbot\App\Callables\Actions\Talker;
use Commune\Chatbot\App\Callables\StageComponents\AskContinue;
use Commune\Chatbot\App\Contexts\TaskDef;
use Commune\Chatbot\OOHost\Context\Depending;
use Commune\Chatbot\OOHost\Context\Exiting;
use Commune\Chatbot\OOHost\Context\Stage;
use Commune\Chatbot\OOHost\Directing\Navigator;
use Commune\Components\Demo\Contexts\WelcomeUser;
use Commune\Platform\Web\Servers\WebServer;
use Commune\Platform\Wechat\Servers\OfficialAccountServer;

/**
 * CommuneChatbot 项目 Demo 的入口
 *
 * @author thirdgerb <therdgerb@gmail.com>
 *
 */
class DemoHome extends TaskDef
{
    public static function __depend(Depending $depending): void
    {
    }

    public function __exiting(Exiting $listener): void
    {
    }

    public function __onStart(Stage $stage): Navigator
    {
        $mem = ReadHistory::from($this);
        // 如果当前语境不是第一次启动, 就直接跳到欢迎用户.
        if ($mem->isStarted === true) {
            return $stage->dialog->goStage('welcome');
        }

        $mem->isStarted = true;

        return $stage->dialog->goStage('description');

    }

    public function __onDescription(Stage $stage) : Navigator
    {
        // 生成连续展示的脚本.
        $scripts = $this->buildScripts($stage);
        $askContinue = new AskContinue($scripts);
        $askContinue->onFinal(Redirector::goStage('welcome'));

        // 用组件化的方式定义流程.
        return $stage->component($askContinue);
    }

    /**
     * 生成首页启动时的欢迎脚本
     *
     * @param Stage $stage
     * @return array
     */
    protected function buildScripts(Stage $stage) : array
    {
        $platformId = $stage
            ->dialog
            ->session
            ->conversation
            ->getChat()
            ->getPlatformId();

        $scripts = [];

        // 自动生成闭包.
        $scripts[] = Talker::say()->info('hyperf-demo.welcome');


        switch ($platformId) {
            case WebServer::class :
                $info = 'hyperf-demo.welcomeToWeb';
                break;

            case OfficialAccountServer::class :
                $info = 'hyperf-demo.welcomeToWechat';
                break;

            default :
                $info = 'hyperf-demo.otherDemos';
        }

        $scripts[] = Talker::say()->info($info);
        $scripts[] = Talker::say()->info('hyperf-demo.enterToDemo');
        return $scripts;
    }

    /**
     * 直接进入欢迎用户语境.
     *
     * @param Stage $stage
     * @return Navigator
     */
    public function __onWelcome(Stage $stage) : Navigator
    {
        return $stage->dependOn(
            WelcomeUser::class,
            Redirector::goFulfill()
        );
    }


}