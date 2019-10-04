## 2019-10-04

-   chatbot 库同步升级.
-   开发了 wechat 端.
-   由于 session 增加了 scene 功能, 所以调整了各端的启动.
-   DuerOS 改动
    -   将 renderer 作为 component 的配置.
-   Hyperf 改动
    -   HyperfBotOption 增加了 shares, 可以设置从 hyperf 传递来的单例.
    -   HyperfBotOption 覆盖 chatbotConfig 的 debug
    -   tinker 增加了对 scene 的支持.
    -   MessageRequest 实现了 chatbot 库的新改动.
    -   增加了对 Hyperf ClientFactory 的桥接.
    -   增加了 HttpBabel 用于各种 http 封装的转义.
    -   增加了各个默认端的独立 redis 配置.
    -   RedisCacheAdapter 实现了对 psr16 的适配. 需要测试.


