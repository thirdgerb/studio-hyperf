<?php


return [
    'app' => [

        'welcome' => "欢迎您! {name}, 这是您第 {times} 次访问!",
        'falwell' => "好的,再见!",

        'wechat' => [
            'officialAccount' => '微信公众号请关注 CommuneChatbot',
            'join' => "在与公众号对话中输入命令:   #join {session}\n\n就可以申请加入了"

        ],

        'ask' => [
            'ifWantIntro' => '您需要我来介绍一下 CommuneChatbot 这个项目吗?'
        ],

        'simpleChat' => [
            'noService' => "对不起! 当前项目没有设置闲聊功能模块, 测试机器人不可用",

            'welcome' => '您好! 欢迎测试闲聊模块!',
            'corpus' => '目前闲聊测试模块已经掌握的语料有 {count} 条',
            'intro' => '您可以通过和我的对话来教我如何回应. 本功能仅供测试, 请不要提供 "不好" 的语料',
            'ask' => '我在听',

            'teach' => '请您教我如何回答: "{say}"',
            'learnEmpty' => "您教我的回复内容为空, 请教我一个有内容的回复 :)",
            'learned' => '好的, 非常感谢您的教导!',

            'reply' => "{reply}",

            'notNature' => "抱歉, {text} 被认为是非自然语言输入, 我不会学习如何回答它.",

        ],

    ],

];
