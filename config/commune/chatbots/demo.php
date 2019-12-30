<?php

use Commune\Studio\Providers;
use Commune\Studio\SessionPipes;

/**
 * 机器人的默认配置.
 *
 * @see \Commune\Chatbot\Config\ChatbotConfig
 */
$chatbot = [

    // 系统用 chatbotName 来隔离会话. 必须要填.
    'chatbotName' => 'commune-studio-demo',

    // 会被 botOption 的 debug 覆盖.
    'debug' => env('CHATBOT_DEBUG', false),

    // ChatServer 使用的类
    'server' => \Commune\Hyperf\Foundations\HyperfChatServer::class,

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
    'components' => include __DIR__ .'/../configs/components.php',

    // 系统默认的服务注册.
    'baseServices' => \Commune\Chatbot\Config\Children\BaseServicesConfig::stub(),

    // 进程级别的服务注册
    'processProviders' => [
        // 注册 feel emotion 模块
        'feeling' => Providers\FeelingServiceProvider::class,
        // register chatbot event
        'event' => Providers\EventServiceProvider::class,
        // 公共的rendering
        'render' =>  Providers\RenderServiceProvider::class,
        // 权限识别
        'ability' => Providers\AbilityServiceProvider::class,
    ],

    // 在worker中注册的服务, 多个请求共享
    'conversationProviders' => [
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

    // 响应消息请求的管道
    'chatbotPipes' => [
        'onUserMessage' => [
            // 发送 conversation, 并且管理所有的异常
            \Commune\Chatbot\App\ChatPipe\UserMessengerPipe::class,
            // 用于锁定 chat, 避免用户输入消息太频繁导致歧义
            \Commune\Chatbot\App\ChatPipe\ChattingPipe::class,
            // 多轮对话管理内核
            \Commune\Chatbot\OOHost\OOHostPipe::class,
        ],
    ],

    // 消息请求加锁的自动过期时间
    'chatLockerExpire' => 3,

    /**
     * 翻译模块配置
     * @see \Commune\Chatbot\Framework\Providers\TranslatorServiceProvider
     */
    'translation' => [
        // 默认配置文件的类型. 也支持 yaml 等.
        'loader' => 'php',
        // 语言包所在的目录, 目录下每一个子目录对应一种语言.
        // 例如 /resources/langs/zh 对应 zh
        // 每个目录里应该都有一个 messages.php 文件, 而且文本变量应该按照 intl 方式定义.
        'resourcesPath' => BASE_PATH . '/resources/langs',
        // 默认使用的语言包.
        'defaultLocale' => 'zh',
        // cache 文件所在路径.
        'cacheDir' => BASE_PATH . '/runtime/cache/langs/',
    ],

    /**
     * 日志文件配置
     * @see Monolog\Handler\StreamHandler
     */
    'logger' => [
        // 日志文件路径
        'path' => BASE_PATH . '/runtime/commune_demo.log',
        // 日志保存的天数, 默认每天会生成一个日志文件
        'days' => 7,
        // 记录日志的级别,
        'level' => env('CHATBOT_DEBUG', false) ? \Psr\Log\LogLevel::DEBUG : \Psr\Log\LogLevel::INFO,
        'bubble' => true,
        // 指定日志文件的权限.
        'permission' => NULL,
        // 写日志文件时是否加锁.
        'locking' => false,
    ],

    // 系统默认的slots, 所有的reply message 都会使用
    // 多维数组会被抹平
    //  例如 self => [ 'name' => 'a'],  会变成 self_name 这样的形式
    // default reply slots
    // multi-dimension array will be flatten to dot pattern
    // such as 'self.name'
    'defaultSlots' => include __DIR__ . '/../configs/slots.php',

    // 系统在一些特殊场景, 默认回复用户的消息
    'defaultMessages' => \Commune\Chatbot\Config\Children\DefaultMessagesConfig::stub(),

    // 多轮对话管理内核的配置
    'host' => [

        // 默认的根语境名
        'rootContextName' => \Commune\Studio\Components\Demo\Contexts\DemoHome::class,

        // 不同场景下的根语境名.
        'sceneContextNames' => [
            'introduce' => 'sw.demo.intro',
            'special' => 'sw.demo.intro.special',
            'game' => \Commune\Components\Demo\Contexts\GameTestCases::class,
            'unheard' => 'unheard-like.episodes.who-is-lizhongwen',
            'story' => 'story.examples.sanguo.changbanpo',
            'maze' => \Commune\Components\Demo\Cases\Maze\MazeInt::getContextName(),
            'nlu' => \Commune\Components\Demo\Contexts\NLTestCases::class,
            'dev' => \Commune\Studio\Components\Demo\Contexts\DevTools::class,
        ],

        // 可 "返回上一步" 的最大次数
        'maxBreakpointHistory' => 2,
        // 单次请求, 多轮对话状态变更的最大次数
        'maxRedirectTimes' => 20,
        // 是否记录多轮对话状态变更路径 到日志
        'logRedirectTracking' => true,
        // Session 的过期时间
        'sessionExpireSeconds' => 3600,
        // 默认扫描, 加载的 Context 类所在路径, 按 psr-4 规范
        'autoloadPsr4' => [
            "Commune\\Studio\\Contexts\\" => BASE_PATH . '/app-studio/Contexts',
        ],

        'sessionPipes' => [
            // event 转 message
            // transfer curtain event messages to other messages
            0 => \Commune\Chatbot\App\SessionPipe\EventMsgPipe::class,
            // 单纯用于测试的管道,#intentName# 模拟命中一个意图.
            // use "#intentName#" pattern to mock intent
            1 => \Commune\Chatbot\App\SessionPipe\MarkedIntentPipe::class,
            // 用户可用的命令.
            2 => SessionPipes\UserCommandsPipe::class,
            // 超级管理员可用的命令. for supervisor only
            3 => SessionPipes\AnalyseCommandsPipe::class,

            // 4. 的位置留给 NLU 中间件

            // 优先级最高的意图, 通常用于导航.
            // 会优先匹配这些意图.
            // highest level intent
            5 => SessionPipes\NavigationIntentsPipe::class,
        ],

        // 通过配置定义的上下文记忆
        'memories' => [
        ],

        // hearing 模块系统默认的 defaultFallback 方法
        // 在 $dialog->hear()->end() 的时候调用.
        'hearingFallback' => \Commune\Components\SimpleChat\Callables\SimpleChatAction::class,
    ],
];


// 根据是否有 rasa 决定是否开启
$hasRasa = env('RASA_API', '');
if (!empty($hasRasa)) {
    $chatbot['host']['sessionPipes'][4] = \Commune\Components\Rasa\RasaSessionPipe::class;
}

return $chatbot;
