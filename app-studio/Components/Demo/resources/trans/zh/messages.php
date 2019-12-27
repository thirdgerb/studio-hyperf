<?php

return [

    'hyperf-demo' => [

        'welcome' => <<<EOF
欢迎来到 {self_name} 项目的 Demo. 当前版本是 {version}.

{self_name} 是一个基于 PHP 开发的开源多轮对话机器人开发框架. 最大的特点是用工程化的方式实现复杂多轮对话管理, 有能力接入各种对话式的通讯平台, 整合各种 NLU (自然语言理解单元) 作为中间件, 实现具备复杂多轮对话能力的机器人. 
EOF
        ,

        'welcomeToWeb'  => <<<EOF
您现在看到的是 Web 版的 Demo, 这是一个 "机器人IM" 的示范, 左侧导航可以点选不同的子机器人进行对话. 

本 Demo 也是一个 web + 对话 双相交互的示范. 对话和鼠标交互共同作用于多轮对话. 与机器人对话时, 点击右上角的 "<>" 图标可以查看当前语境的源代码. 

微信版的 Demo 在公众号 "{self_name}", DuerOS (百度智能音箱) 的 Demo 在技能 "三国群英传", 欢迎查看.
EOF
        ,

        'welcomeToWechat' => <<<EOF
您现在看到的是微信版的 Demo. Web 版的 Demo ( web + 对话 双相交互的 "对话IM" demo ) 请打开官网 https://commmunechatbot.com .

DuerOS (百度智能音箱) 的 Demo 在技能 "三国群英传", 欢迎查看.
EOF
        ,

        'otherDemos' => <<<EOF
本项目的 Web 版 Demo ( web + 对话 双相交互的 "对话IM" demo ) 请打开官网 https://commmunechatbot.com .

微信版的 Demo 在公众号 "{self_name}".

DuerOS (百度智能音箱) 的 Demo 在技能 "三国群英传", 欢迎查看.
EOF
        ,

        'enterToDemo' => '接下来进入正式的 demo 内容. ',

        'devTools' => <<<EOF
多轮对话机器人, 就像 shell 一样, 本身就可以作为独立的开发工具使用.

由于对话开发相对简便, 它比开发一个 web 版, app 版的工具要容易太多了. 而且一次开发, 可以部署到各种 IM 平台上使用。

最有意思的是， 我们可以用多轮对话开发出管理多轮对话机器人的工具。 
EOF
        ,

    ],
];

