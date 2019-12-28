# CommuneChatbot Studio

本项目是为 [CommuneChatbot](https://github.com/thirdgerb/chatbot) 开发的工作站, 用于搭建生产环境下可用的多轮对话机器人.

[CommuneChatbot](https://github.com/thirdgerb/chatbot) 是一个多轮对话机器人框架, 拥有两部分功能:

-   framework: 作为对话机器人框架, 可以接入即时通讯或语音平台服务端, 整合NLU, 搭建对话机器人
-   OOHost: 多轮对话内核, 可以用工程化的手段开发能实现复杂多轮对话的机器人.

工作站使用 [Swoole 4.3+](https://github.com/swoole/swoole-src) 提供高性能的服务, 基于swoole 协程框架 [Hyperf](https://github.com/hyperf-cloud/hyperf-skeleton) 开发.

## Demo

目前的 Demo 有:

* 官方网站 : https://communechatbot.com/
* 开发文档 : https://communechatbot.com/docs/zh-cn/
* 微信公众号 Demo: 搜索 "CommuneChatbot"
* 百度智能音箱: 对音箱说 "打开三国群英传", "打开方向迷宫"

## 项目构成

- [Chatbot](https://github.com/thirdgerb/chatbot) : 机器人核心框架
- [Studio](https://github.com/thirdgerb/studio-hyperf) : 工作站, 基于 [Swoole](https://github.com/swoole/swoole-src) + [Hyperf](https://github.com/hyperf/hyperf) 开发, 可创建和运行应用
- [Chatbot-book](https://github.com/thirdgerb/chatbot-book) : 机器人开发手册项目

## 快速启动

安装项目:

    # 安装项目
    git clone https://github.com/thirdgerb/studio-hyperf.git
    cd studio-hyperf/

    # composer 安装依赖
    composer install

或者使用 composer 安装

    composer create-project commune/studio-hyperf

确认依赖:

- php >= 7.2
- php 基础扩展
- swoole >= 4.4
- php 扩展 [intl](https://www.php.net/manual/en/book.intl.php) 用于国际化

运行命令行 demo :

    php bin/hyperf.php commune:tinker

更多细节请查看 [CommuneChatbot 手册](https://communechatbot.com/docs/zh-cn/).

用 CommuneChatbot 开发多轮对话, 一个简单的示例如下 :

```php
/**
 * Context for hello world
 *
 * @property string $name userName
 */
class HelloWorldContext extends OOContext
{
    const DESCRIPTION = 'hello world!';

    // stage "start"
    public function __onStart(Stage $stage) : Navigator
    {
        return $stage->buildTalk()

            // send message to user
            ->info('hello world!!')

            // ask user name
            ->goStage('askName')
    }

    // stage "askName"
    public function __onAskName(Stage $stage) : Navigator
    {
        return $stage->buildTalk()
            // ask user for name
            ->askVerbal('How may I address you?')

            // wait for user message
            ->hearing()

            // message is answer
            ->isAnswer(function(Answer $answer, Dialog $dialog) {

                // set Context memory
                $this->name = $answer->toResult();

                // go to "menu" stage
                return $this->goStage('menu');
            })

            // finish building Hearing AIP
            ->end();
    }

    // stage "menu"
    public function __onMenu(Stage $stage) : Navigator
    {
        // build menu component
        $menu = new Menu(
            // menu question
            'What can I help you?',

            // answer suggesions
            [

                // go to play game context
                PlayGameContext::class,

                // go to order drinks
                OrderDrinkContext::class,

                // go to simple chat
                SimpleChatContext::class,
            ]
        );

        return $stage

            // after target context fulfilled
            ->onFallback(function(Dialog $dialog) {
                // repeat current menu stage
                return $dialog->repeat();
            });

            // use component
            ->component($menu);
    }
}
```



