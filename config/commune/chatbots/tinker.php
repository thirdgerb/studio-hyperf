<?php

use Commune\Studio\Providers;
use Commune\Studio\SessionPipes;

/**
 * 机器人的默认配置.
 * 用于 commune:tinker 命令.
 * 可以用此命令来进行本地无数据的调试.
 *
 * @see \Commune\Chatbot\Config\ChatbotConfig
 */
return [

    'chatbotName' => 'tinker',

    'server' => \Commune\Hyperf\Servers\Tinker\TinkerChatServer::class,

    'debug' => true,

    // 在这里可以预先绑定一些用 Option 类封装的配置.
    // 会将该配置预绑定到worker容器上, 作为单例.
    // 有三种绑定方式:
    // 1. 只写类名, 默认使用 stub 里的配置.
    // 2. 类名 => 数组,  会用数组内的值覆盖 stub 的相关参数.
    // 3. 类名 => 子类名, 会用子类的实例来绑定父类类名.
    'configBindings' => [
        \Commune\Hyperf\Servers\Tinker\TinkerOption::class,
    ],


    // 预加载的组件. 使用方法类似 configBindings
    // 但component 不仅会预加载配置, 而且还能注册各种组件, 进行初始化等.
    'components' => include __DIR__ .'/../configs/components.php',

    'baseServices' => \Commune\Chatbot\Config\Children\BaseServicesConfig::stub(),

    // 进程级别的服务注册
    'processProviders' => [

        // 注册 feel emotion 模块
        'feeling' => Providers\FeelingServiceProvider::class,
        // register chatbot event
        'event' => Providers\EventServiceProvider::class,
        // 公共d的rendering
        'render' =>  Providers\RenderServiceProvider::class,
        // 权限识别
        'ability' => Providers\FakeAbilityServiceProvider::class,
    ],

    // 在worker中注册的服务, 多个请求共享
    'conversationProviders' => [
        // hyperf client driver . redis, db
        // hyperf 的协程客户端
        'client' => \Commune\Hyperf\Foundations\Providers\ClientDriverServiceProvider::class,

        // cache adapter driver
        // 实现 chatbot 需要的 cache adapter
        // test only driver
        'cache' => \Commune\Chatbot\App\Drivers\Demo\CacheServiceProvider::class,

        // test only driver
        'session' => \Commune\Chatbot\App\Drivers\Demo\SessionServiceProvider::class,
    ],

    'chatbotPipes' =>
        [
            'onUserMessage' => [
                \Commune\Chatbot\App\ChatPipe\UserMessengerPipe::class,
                \Commune\Chatbot\App\ChatPipe\ChattingPipe::class,
                \Commune\Chatbot\OOHost\OOHostPipe::class,
            ],
        ],

    // translation module
    'translation' => [
        'loader' => 'php',
        'resourcesPath' => BASE_PATH . '/resources/langs',
        'defaultLocale' => 'zh',
        'cacheDir' => NULL,
    ],

    // logger module
    'logger' => [
        'name' => 'chatbot',
        'path' => BASE_PATH . '/runtime/logs/tinker.log',
        'days' => 0,
        'level' => 'debug',
        'bubble' => true,
        'permission' => NULL,
        'locking' => false,
    ],

    'defaultMessages' => [
        'platformNotAvailable' => 'system.platformNotAvailable',
        'chatIsTooBusy' => 'system.chatIsTooBusy',
        'systemError' => 'system.systemError',
        'farewell' => 'dialog.farewell',
        'messageMissMatched' => 'dialog.missMatched',
    ],

    'defaultSlots' => include __DIR__ . '/../configs/slots.php',

    'host' => [

        // 系统默认的slots, 所有的reply message 都会使用
        // 多维数组会被抹平为 self.name 这样的形式
        // default reply slots
        // multi-dimension array will be flatten to dot pattern
        // such as 'self.name'
        'slots' => [
            //'self' => [
            //    'name' => 'CommuneChatbot',
            //    'project' => 'commune/chatbot',
            //    'fullname' => 'commune/chatbot demo',
            //    'author' => 'thirdgerb',
            //    'email' => 'thirdgerb@gmail.com',
            //    'desc' => '多轮对话机器人开发框架',
            //]
        ],

        'rootContextName' => \Commune\Studio\Components\Demo\Contexts\DemoHome::class,

        'sceneContextNames' => [
            'test' => \Commune\Components\Demo\Contexts\DemoHome::class,
        ],

        'sessionPipes' => [
            // event 转 message
            // transfer curtain event messages to other messages
            \Commune\Chatbot\App\SessionPipe\EventMsgPipe::class,
            // 用户可用的命令.
            SessionPipes\UserCommandsPipe::class,
            // 超级管理员可用的命令. for supervisor only
            SessionPipes\AnalyseCommandsPipe::class,
            // 单纯用于测试的管道,#intentName# 模拟命中一个意图.
            // use "#intentName#" pattern to mock intent
            \Commune\Chatbot\App\SessionPipe\MarkedIntentPipe::class,
            // 优先级最高的意图, 通常用于导航.
            // 会优先匹配这些意图.
            // highest level intent
            SessionPipes\NavigationIntentsPipe::class,
        ],

        'hearingFallback' => \Commune\Components\SimpleChat\Callables\SimpleChatAction::class,
    ],

];
