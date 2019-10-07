<?php

use Commune\Studio\Providers;
use Commune\Studio\SessionPipes;

/**
 * 机器人的默认配置.
 *
 * @see \Commune\Chatbot\Config\ChatbotConfig
 */
return [

    // 系统用 chatbotName 来隔离会话. 必须要填.
    'chatbotName' => 'commune-studio-demo',

    // 会被 botOption 的 debug 覆盖.
    'debug' => true,

    // 在这里可以预先绑定一些用 Option 类封装的配置.
    // 会将该配置预绑定到worker容器上, 作为单例.
    // 有三种绑定方式:
    // 1. 只写类名, 默认使用 stub 里的配置.
    // 2. 类名 => 数组,  会用数组内的值覆盖 stub 的相关参数.
    // 3. 类名 => 子类名, 会用子类的实例来绑定父类类名.
    'configBindings' => [
    ],


    // 预加载的组件. 使用方法类似 configBindings
    // 但component 不仅会预加载配置, 而且还能注册各种组件, 进行初始化等.
    'components' => [
        \Commune\Hyperf\Demo\HyperfDemoComponent::class,
        \Commune\Components\StoryComponent::class,
    ],

    // 系统默认的服务注册.
    'baseServices' => \Commune\Chatbot\Config\Children\BaseServicesConfig::stub(),

    // 进程级别的服务注册
    'processProviders' => [
        // 基础service
        'exp' => Providers\ExceptionHandlerServiceProvider::class,
        // 注册 feel emotion 模块
        'feeling' => Providers\FeelingServiceProvider::class,
        // register chatbot event
        'event' => Providers\EventServiceProvider::class,
        // 公共的rendering
        'render' =>  Providers\RenderServiceProvider::class,
    ],

    // 在worker中注册的服务, 多个请求共享
    'conversationProviders' => [
        // 权限识别
        'ability' => Providers\AbilityServiceProvider::class,
        // hyperf client driver . redis, db
        // hyperf 的协程客户端
        'client' => \Commune\Hyperf\Foundations\Providers\ClientDriverServiceProvider::class,
        // cache adapter driver
        // 实现 chatbot 需要的 cache adapter
        'cache' => \Commune\Hyperf\Foundations\Providers\CacheServiceProvider::class,
        // oo host session driver
        'session' => \Commune\Hyperf\Foundations\Providers\SessionServiceProvider::class,
        // message request service
        'message' => \Commune\Hyperf\Foundations\Providers\MessageQueueServiceProvider::class,
    ],

    'chatbotPipes' =>
        [
            'onUserMessage' => [
                \Commune\Chatbot\App\ChatPipe\MessengerPipe::class,
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
        'path' => BASE_PATH . '/runtime/commune_demo.log',
        'days' => 7,
        'level' => 'debug',
        'bubble' => true,
        'permission' => NULL,
        'locking' => false,
    ],

    'defaultMessages' => \Commune\Chatbot\Config\Children\DefaultMessagesConfig::stub(),

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

        // 默认的根语境名
        'rootContextName' => \Commune\Hyperf\Demo\Contexts\TestCase::class,

        // 不同场景下的根语境名.
        'sceneContextNames' => [
            'maze' => \Commune\Demo\App\Cases\Maze\MazeInt::getContextName(),
            'story' => 'story.examples.sanguo.changbanpo',
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

        // hearing 模块系统默认的 defaultFallback 方法
        // 在 $dialog->hear()->end() 的时候调用.
        'hearingFallback' => null,
    ],

];
