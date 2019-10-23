<?php

return [

    // 官方的基础 demo
    \Commune\Hyperf\Demo\HyperfDemoComponent::class,

    // 系统自带的 NLU 单元 配置
    // 包括本地语料库, 自然语言单元等配置
    \Commune\Chatbot\OOHost\NLU\NLUComponent::class => [

        // 可以使用的 nlu 服务. 可以自动同步
        'nluServices' => [
            \Commune\Components\Rasa\Services\RasaService::class,
        ],

        'nluLogger' => \Commune\Chatbot\OOHost\NLU\Predefined\SimpleNLULogger::class,


        'intentRootStorage' => [
            'meta' => \Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta::class,
            'config' => [
                'path' => BASE_PATH . '/resources/nlu/intents/',
                'isDir' => true,
            ],
        ],

        'entityRootStorage' => [
            'meta' => \Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta::class,
            'config' => [
                'path' => BASE_PATH . '/resources/nlu/entities/',
                'isDir' => true,
            ],
        ],

        'synonymRootStorage' => [
            'meta' => \Commune\Support\OptionRepo\Storage\Yaml\YamlStorageMeta::class,
            'config' => [
                'path' => BASE_PATH . '/resources/nlu/synonyms.yml',
                'isDir' => false,
            ],
        ],


    ],


    // 情景游戏组件.
    \Commune\Components\Story\StoryComponent::class,

    // 闲聊组件
    \Commune\Components\SimpleChat\SimpleChatComponent::class,

    // 简单的文件 wiki 组件
    \Commune\Components\SimpleWiki\SimpleWikiComponent::class,

    // rasa 组件
    \Commune\Components\Rasa\RasaComponent::class => [
        'server' => env('RASA_API', 'localhost:5050'),
        'output' => realpath(__DIR__ . '/../../../rasa-demo/data/nlu.md'),
    ],
];

