## 2019-10-19

-   删掉了示范作用的 apps 目录.
-   web 端改动
    -   web component 增加了输入长度的限制.
    -   web request 修改了返回值策略, 现在一般返回 status 200, 用code 标记错误.
    -   web 端对 link 消息有了独立的渲染模板.
-   基于 chatbot gc 的改动, 修改了 sessionDriver 的实现.
