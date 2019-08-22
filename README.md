Developing...

# 介绍

本项目是为 [CommuneChatbot](https://github.com/thirdgerb/chatbot) 开发的工作站, 用于搭建生产环境下可用的多轮对话机器人. 目前尚在开发中.


[CommuneChatbot](https://github.com/thirdgerb/chatbot) 是一个多轮对话机器人框架, 拥有两部分功能:

-   framework: 作为对话机器人框架, 可以接入即时通讯或语音平台服务端, 整合NLU, 搭建对话机器人
-   OOHost: 多轮对话内核, 可以用工程化的手段开发能实现复杂多轮对话的机器人.

本工作站使用 [Swoole 4.3+](https://github.com/swoole/swoole-src) 提供高性能的服务, 基于swoole 协程框架 [Hyperf](https://github.com/hyperf-cloud/hyperf-skeleton) 开发.

# 计划开发中内容

-   完善 CommuneChatbot 功能.
-   默认系统后台
-   自带 NLU 解决方案
    -   FAQ 模块
    -   语义模块 (基于rasa, 增强对中文支持? )
-   NLU 中间件
    -   Rasa 版本
    -   百度UNIT 版本
-   应用
    -   微信公众号机器人
    -   百度智能音箱机器人
    -   <del>微信机器人</del>(官方禁用了大多数微信web版)
-   功能组件
    -   闲聊
    -   应用组件管理
    -   NLU 管理单元
-   应用组件
    -   调查问卷引擎 ?
    -   FAQ 引擎 ?
    -   文字游戏引擎 ?

