<?php

use \Commune\Chatbot\App\Callables\Actions\Redirector;

return [

    // 官方的基础 demo
    \Commune\Studio\Components\Demo\HyperfDemoComponent::class,

    // 系统自带的 NLU 单元 配置
    // 包括本地语料库, 自然语言单元等配置
    \Commune\Chatbot\OOHost\NLU\NLUComponent::class => [

        // 可以使用的 nlu 服务. 可以自动同步
        'nluServices' => [
            \Commune\Components\Rasa\Services\RasaService::class,
        ],

        // nlu 的默认日志
        'nluLogger' => \Commune\Chatbot\OOHost\NLU\Providers\NLULoggerServiceProvider::class,

        // 意图语料库的根介质
        'intentRootStorage' => [
            'meta' => \Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta::class,
            'config' => [
                // 意图语料所在文件夹
                'path' => BASE_PATH . '/resources/nlu/intents/',
                'isDir' => true,
            ],
        ],

        // 实体词典的根介质
        'entityRootStorage' => [
            'meta' => \Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta::class,
            'config' => [
                // 实体词典所在文件夹
                'path' => BASE_PATH . '/resources/nlu/entities/',
                'isDir' => true,
            ],
        ],

        'synonymRootStorage' => [
            'meta' => \Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta::class,
            'config' => [
                // 同义词词典所在文件
                'path' => BASE_PATH . '/resources/nlu/synonyms.yml',
                'isDir' => false,
            ],
        ],
    ],


    // 对话冒险游戏组件.
    \Commune\Components\Story\StoryComponent::class,

    // 配置建议
    //\Commune\Components\Story\StoryComponent::class => [
    //    // 语言包所在目录
    //    'translationPath' => BASE_PATH . '/resources/story/langs',
    //    // 意图所在目录
    //    'intentsPath' => BASE_PATH .'/resources/story/nlu/intents.yml',
    //    'rootStorage' => [
    //        'meta' => \Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta::class,
    //        'config' => [
    //            // 故事配置所在目录
    //            'path' => BASE_PATH . '/resources/stories/',
    //            'isDir' => true,
    //        ],
    //    ],
    //],

    // 疑案追声模式 demo 组件
    \Commune\Components\UnheardLike\UnheardLikeComponent::class,

    // 闲聊组件
    \Commune\Components\SimpleChat\SimpleChatComponent::class,

    // 闲聊组件配置建议
    //\Commune\Components\SimpleChat\SimpleChatComponent::class => [
    //    'rootStorage' => [
    //        'meta' => \Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta::class,
    //        'config' => [
    //            // 闲聊配置文件路径
    //            'path' => BASE_PATH . '/resources/chat/example.yml',
    //            // 表示是单个文件
    //            'isDir' => false,
    //        ],
    //    ],
    //],

    // 简单的文件 wiki 组件
    \Commune\Components\SimpleWiki\SimpleWikiComponent::class,

    // 本地内配置
    //\Commune\Components\SimpleWiki\SimpleWikiComponent::class =>[
    //    // wiki 的分组
    //    'groups' => [
    //        // 系统自带的 demo
    //        [
    //            'id' => 'demo',
    //            'intentAlias' => [
    //                // alias => intentName
    //            ],
    //            'defaultSuggestions' => [
    //                // default suggestions
    //                '重复内容' => Redirector::goRestart(),
    //                '返回上一层' => Redirector::goFulfill(),
    //                '退出' => Redirector::goCancel(),
    //            ],
    //            'question' => 'ask.needs',
    //            'askContinue' => 'ask.continue',
    //            'messagePrefix' => 'demo.simpleWiki',
    //        ],
    //    ],
    //    // 语言包
    //    'langPath' => BASE_PATH .'/resources/wiki/langs',
    //    // 存储介质
    //    'rootStorage' => [
    //        'meta' => \Commune\Components\SimpleWiki\Options\YamlPathStorageMeta::class,
    //        'config' => [
    //            'path' => BASE_PATH .'/resources/wiki/config',
    //            'depth' => '>= 1', // 第一层目录会作为 group 的分组ID.
    //            'isDir' => true,
    //        ],
    //    ],
    //],

    // rasa 组件
    \Commune\Components\Rasa\RasaComponent::class => [
        // 服务端地址
        'server' => env('RASA_API', 'localhost:5050'),
        // 语料库输出地址
        'output' => BASE_PATH . '/rasa-demo/data/nlu.md',
        // domain 配置地址.
        'domainOutput' => BASE_PATH . '/rasa-demo/domain.yml',
    ],
];

