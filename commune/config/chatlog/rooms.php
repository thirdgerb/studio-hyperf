<?php

use Commune\Blueprint\Framework\Auth\Supervise;
use Commune\Chatlog\SocketIO\Coms\RoomOption;

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

        'title' => '联系管理员',
        'desc' => '与管理员通讯',
        'icon' => 'mdi-account-question',

        'category' => 'supervisor',
        'closable' => false,
        'bot' => false,
        'botName' => 'commune',
        'entry' => \Commune\Components\HeedFallback\Context\TeachTasks::genUcl()->encode(),

        'level' => Supervise::SUPERVISOR,
        'levelMode' => RoomOption::LEVEL_MODE_BELOW,

        'supervised' => false,

        'autoJoin' => true,
        'recommend' => false,
    ],
];
