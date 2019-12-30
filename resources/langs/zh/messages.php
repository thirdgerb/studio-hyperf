<?php

/**
 * 默认需要有的 文本内容。
 */
return [
    'hello' => [
        'world'  => '您好,世界, 你输入的是{input}',
        'user' => '您好, {name}, 欢迎您来到 {appName} '
    ],

    'system' => [
        'platformNotAvailable' => '系统不可用',
        'chatIsTooBusy' => '输入太频繁',
        'systemError' => '系统错误',
        'unsupported' => '输入消息类型暂不支持',
    ],

    'dialog' => [
        'farewell' => '再见!',
        'missMatched' => '没有明白什么意思',
        'noHelpInfoExists' => '不好意思, 当前内容没有帮助信息. 可以输入 #home 回到开头, 或输入 #repeat 重新开始对话. 输入 #help 查看更多指令.',
    ],

    'command' => [
        'notExists' => '命令 {name} 不存在',
        'invalidArgument' => '参数 {name} 不正确',
        'notValid' => '{name} 不是合法的命令',
        'available' => "可用的命令: \n{available}",
        'contextNotExists' => 'context {contextName} 未注册',
        'navigateToContext' => '导航到 context {contextName}',
    ],

    'ask' => [
        'name' => '您好! 请问我该如何称呼您',
        'entity' => '请输入{entity_name}',
        'needs' => '您可能需要:',
        'needMore' => '您还有别的需要吗?',
        'continue' => '输入任意信息继续',
        'yes' => 'yes',
        'no' => 'no',
    ],

    'question' => [
        'default' => "{query}{default}\n{suggestions}",
    ],

    'errors' => [
        'badAnswer' => '您输入的信息不正确, 请重新输入',
        'mustBeSupervisor' => '只有管理员允许访问当前语境',
    ],

];
