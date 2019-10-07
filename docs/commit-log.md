## 2019-10-07 (2)

-   加入了 Web 端. 完成了 web app 的初步开发
-   超管功能加入
    -   isSupervisor & whosyourdaddy 命令允许用咒语变成超级管理员
    -   去掉了用户有风险的 where 和 whoami 命令. 增加了部分 navigation
-   DuerOS 改动
    -   渲染文字不再自动加 '.' 号. 开发者自己要加.
    -   配合 chatbot, 修改了问题的 suggestions 渲染的bug
-   小改动
    -   修复了 RedisCacheAdapter 一直没有正确 unlock 的 bug
    -   去掉了一些 demo 性质的事件监听
    -   简单优化了情景游戏的流程.
    -   AbstractMessageRequest 加入了 runningSpy


