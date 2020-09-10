<?php

use Commune\Chatbot\Hyperf;
use Commune\Components;
use Commune\Ghost;
use Commune\Support\Utils\StringUtils;
use Commune\Blueprint\CommuneEnv;
use Commune\Chatbot\Hyperf\Config\Shells;
use Commune\Chatbot\Hyperf\Config\HfHostConfig;
use Commune\Chatbot\Hyperf\Config\Platforms\StdioConsolePlatformConfig;
use Commune\Chatbot\Hyperf\Config\Platforms\StdioShellPlatformConfig;
use Commune\Chatbot\Hyperf\Config\Platforms\TcpGhostPlatformConfig;
use Commune\Chatlog\ChatlogSocketIOPlatformConfig;
use Commune\Platform\Wechat\WechatPlatformConfig;


/**
 * Host 的配置.
 */
return new HfHostConfig([
    'id' => 'commune_studio',
    'name' => 'commune_studio',

    'ghost' => new Hyperf\Config\HfGhostConfig([
        'id' => 'commune',
        'name' => 'commune chatbot',


        'defaultContextName' => 'app.markdown.kanban',
        'sceneContextNames' => [
            Components\Demo\Maze\Maze::genUcl()->encode(),
            Components\HeedFallback\Context\TeachTasks::genUcl()->encode(),
            Ghost\Predefined\Manager\NLUManagerContext::genUcl()->encode(),
            \Commune\App\Contexts\SimpleChatContext::genUcl()->encode(),
            'md.demo.*',
            'app.markdown.*'
        ],

        'mindPsr4Registers' => [
            "Commune\\App\\" => StringUtils::gluePath(
                CommuneEnv::getBasePath(),
                'app'
            ),

        ],

        'components' => [
            // 内部测试用例
            Components\Demo\DemoComponent::class,
            // 树形结构对话
            Components\Tree\TreeComponent::class,

            // markdown 文库
            Components\Markdown\MarkdownComponent::class => [

                'reset' => CommuneEnv::isResetRegistry(),
                'groups' => [
                    [
                        'groupName' => 'studioApp',
                        'resourceDir' => StringUtils::gluePath(
                            CommuneEnv::getResourcePath(),
                            'markdown'
                        ),
                        // 命名空间 + 文件的相对路径 = document id
                        'namespace' => 'app.markdown',

                    ],
                    Components\Markdown\Options\MDGroupOption::defaultOption(),
                ],
                'docStorage' => [
                    'wrapper' => Hyperf\Coms\Storage\HfDBStorageOption::class,
                ],
                'sectionStorage' => [
                    'wrapper' => Hyperf\Coms\Storage\HfDBStorageOption::class,
                ],
            ],


            // heed fallback
            Components\HeedFallback\HeedFallbackComponent::class => [
                'strategies' => [
                    Components\HeedFallback\HeedFallbackComponent::defaultStrategy(),

                ],

                'storage' => [
                    'wrapper' => Hyperf\Coms\Storage\HfDBStorageOption::class,
                ],

                'sceneRepository' => Hyperf\Coms\HeedFallback\IFallbackSceneRepository::class,
            ],

            // SpaCy-NLU
            Components\SpaCyNLU\SpaCyNLUComponent::class => [
                'host' => env('SPACY_NLU_HOST', '127.0.0.1:10830'),
                'requestTimeOut' => 0.3,

                'nluModuleConfig' => [
                    'matchLimit' => 5,
                    'threshold' => 0.75,
                    'dataPath' => env('SPACY_NLU_INTENTS_DATA', __DIR__ . '/resources/data/intents.json'),
                ],
                'chatModuleConfig' => [
                    'threshold' => 0.75,
                    'dataPath' => env('SPACY_NLU_CHATS_DATA', __DIR__ . '/resources/data/chats.json'),
                ],

                'httpClient' => Hyperf\Coms\SpaCyNLU\HFSpaCyNLUClient::class,
            ],
        ],

    ]),


    'shells' => [

        /**
         * 本地 Console 的 Shell 端.
         */
        new Shells\HfShellConfig([
            'id' => 'console',
            'name' => 'console',
        ]),

        // chatlog shell
        new Shells\HfShellConfig([
            'id' => 'chatlog',
            'name' => 'chatlog shell',
        ]),

        // wechat shell
        new Shells\HfShellConfig([
            'id' => 'wechat',
            'name' => 'wechat shell',
        ]),
    ],

    'platforms' => [

        new TcpGhostPlatformConfig([
            'id' => 'tcp_ghost',
            'name' => 'ghost服务端',
            'desc' => '基于 Swoole 协程实现的 Ghost Tcp 服务端. 使用 Babel 类传输协议.',
            'bootShell' => null,
            'bootGhost' => true,
        ]),

        new StdioConsolePlatformConfig([
            'id' => 'stdio_console',

            'name' => 'stdio 本地端',
            'desc' => '使用 Clue\React\Stdio 实现的本地机器人',

            'bootShell' => 'console',
            'bootGhost' => true,

        ]),

        new StdioShellPlatformConfig([
            'id' => 'stdio_shell',

            'name' => 'stdio shell',
            'desc' => '使用 Clue\React\Stdio 实现的本地 Shell',

            'bootShell' => 'console',

        ]),

        // socket io
        new ChatlogSocketIOPlatformConfig([
            'id' => 'chatlog_socketio',
            'name' => 'chatlog socket.io 平台',
            'desc' => '基于 Socket.io 启动的 Websocket 平台, 与前端对接',
            'bootShell' => 'chatlog',
        ]),

        new WechatPlatformConfig([
            'id' => 'wechat',
            'name' => 'wechat official account server',
            'desc' => '基于 EasyWechat 的微信公众号的服务端',
            'bootShell' => 'wechat',
        ])
    ],
]);
