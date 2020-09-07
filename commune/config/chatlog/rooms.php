<?php

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Chatlog\SocketIO\Coms\RoomOption;
use Commune\Components\Demo\Maze\Maze;

return [

    // commune 助手. 机器人起点
    [
        'scene' => 'commune',
        'private' => true,

        'title' => 'Commune',
        'desc' => 'Commune 对话机器人入口',
        'icon' => 'mdi-robot',

        'category' => 'commune',
        'closable' => false,

        'bot' => true,
        'level' => Supervise::GUEST,
        'levelMode' => RoomOption::LEVEL_MODE_ABOVE,

        'supervised' => false,
        'autoJoin' => true,
        'recommend' => false,
    ],

    // commune 助手. 机器人起点
    [
        'scene' => 'maze',
        'private' => true,

        'title' => '方向迷宫',
        'desc' => '方向迷宫小游戏',
        'icon' => 'mdi-gamepad-square',

        'category' => 'commune',
        'closable' => true,

        'bot' => true,
        'botName' => '方向迷宫',
        'entry' => Maze::genUcl()->encode(),

        'level' => Supervise::GUEST,
        'levelMode' => RoomOption::LEVEL_MODE_ABOVE,

        'supervised' => false,
        'autoJoin' => false,
        'recommend' => true,
    ],
    // commune 交流群.
    [
        'scene' => 'commune-chat',
        'private' => false,

        'title' => '交流群',
        'desc' => 'Commune 项目交流群',
        'icon' => 'mdi-forum',

        'category' => 'commune',
        'closable' => false,
        'bot' => null,

        'level' => Supervise::GUEST,
        'levelMode' => RoomOption::LEVEL_MODE_ABOVE,

        'supervised' => false,

        'autoJoin' => true,
        'recommend' => false,
    ],

    // 与作者交流.
    [
        'scene' => 'author',
        'private' => true,

        'title' => '联系管理员',
        'desc' => '与管理员通讯',
        'icon' => 'mdi-account-question',

        'category' => 'commune',
        'closable' => true,
        'bot' => null,

        'level' => Supervise::USER,
        'levelMode' => RoomOption::LEVEL_MODE_BELOW,

        'supervised' => true,

        'autoJoin' => false,
        'recommend' => true,
    ],

    // commune 项目介绍.
    [
        'scene' => 'commune-intro',
        'private' => true,

        'title' => 'Commune项目介绍',
        'desc' => 'Commune Chatbot v0.2 版介绍',
        'icon' => 'mdi-book-open-blank-variant',

        'category' => 'commune',
        'closable' => true,
        'bot' => true,
        'botName' => 'Commune项目介绍',
        'entry' => 'md.demo.commune_v2_intro',

        'level' => Supervise::USER,
        'levelMode' => RoomOption::LEVEL_MODE_BELOW,

        'supervised' => false,

        'autoJoin' => false,
        'recommend' => true,
    ],


    // 与作者交流.
    [
        'scene' => 'chatbot',
        'private' => true,

        'title' => '闲聊机器人',
        'desc' => '测试用闲聊机器人模块',
        'icon' => 'mdi-robot',

        'category' => 'commune',
        'closable' => true,
        'bot' => true,
        'botName' => 'iRobot',
        'entry' => \Commune\App\Contexts\SimpleChatContext::genUcl()->encode(),

        'level' => Supervise::GUEST,
        'levelMode' => RoomOption::LEVEL_MODE_ABOVE,

        'supervised' => false,
        'autoJoin' => false,
        'recommend' => true,
    ],

    // 对话式视频介绍.
    [
        'scene' => 'conversational-video-demo',
        'private' => true,

        'title' => '对话式视频 Demo',
        'desc' => '对话式视频相关介绍',
        'icon' => 'mdi-book-open-blank-variant',

        'category' => 'commune',
        'closable' => true,
        'bot' => true,
        'botName' => '对话式视频介绍',
        'entry' => 'md.demo.conversational_video_app',

        'level' => Supervise::USER,
        'levelMode' => RoomOption::LEVEL_MODE_BELOW,

        'supervised' => false,

        'autoJoin' => false,
        'recommend' => true,
    ],

    /*---------- 管理员房间 ----------*/

    // supervisor 管理员群.
    [
        'scene' => 'supervisor',
        'private' => false,

        'title' => '管理员会话',
        'desc' => 'Commune 管理员会话',
        'icon' => 'mdi-account-supervisor',

        'category' => 'supervisor',
        'closable' => false,
        'bot' => false,

        'level' => Supervise::SUPERVISOR,
        'levelMode' => RoomOption::LEVEL_MODE_ABOVE,

        'supervised' => false,
        'autoJoin' => true,
        'recommend' => false,
    ],

    // 机器人教学.
    [
        'scene' => 'teacher',
        'private' => false,

        'title' => '机器人教学',
        'desc' => '机器人教学任务',
        'icon' => 'mdi-teach',

        'category' => 'supervisor',
        'closable' => false,
        'bot' => true,
        'botName' => 'commune',
        'entry' => \Commune\Components\HeedFallback\Context\TeachTasks::genUcl()->encode(),

        'level' => Supervise::SUPERVISOR,
        'levelMode' => RoomOption::LEVEL_MODE_ABOVE,

        'supervised' => false,

        'autoJoin' => false,
        'recommend' => true,
    ],

    // 机器人教学.
    [
        'scene' => 'nluManager',
        'private' => true,

        'title' => 'NLU 管理',
        'desc' => 'NLU 管理',
        'icon' => 'mdi-tools',

        'category' => 'supervisor',
        'closable' => true,
        'bot' => true,
        'botName' => 'commune',
        'entry' => \Commune\Ghost\Predefined\Manager\NLUManagerContext::genUcl()->encode(),

        'level' => Supervise::SUPERVISOR,
        'levelMode' => RoomOption::LEVEL_MODE_ABOVE,

        'supervised' => false,

        'autoJoin' => false,
        'recommend' => true,
    ],
];
