# CommuneChatbot Studio v0.2

0.2 版本正在开发中... 应用开发完后正式撰写文档

- 展示网站: https://communechatbot.com/chatlog
- v0.1网站: https://communechatbot.com/
- v0.1分枝: https://github.com/thirdgerb/chatbot/tree/v0.1.x
- v0.1文档: https://communechatbot.com/docs/#/


微信公众号 CommuneChatbot，qq群：907985715 欢迎交流！

相关项目：

- chatbot: https://github.com/thirdgerb/chatbot
- hyperf 对接: https://github.com/thirdgerb/chatbot-hyperf
- 前端项目: https://github.com/thirdgerb/chatlog-web
- nlu单元: https://github.com/thirdgerb/spacy-nlu


## 运行本地 Demo

如果想尝试运行本地 demo, 请确保:

- php >=7.3
- swoole >= 4.5 (最好是最新版本)
- 本地安装了 redis
- 本地安装了 mysql

机器人的默认配置文件地址在 [/commune/config/host.php](/commune/config/host.php)

### 安装项目

克隆仓库

    git clone https://github.com/thirdgerb/studio-hyperf.git

进入目录, 使用 composer 安装依赖

    composer install

修改环境变量文件 ```./.env``` 配置 mysql, redis 等. 具体可参考 [hyperf 配置](https://hyperf.wiki/2.0/#/zh-cn/config?id=%e7%8e%af%e5%a2%83%e5%8f%98%e9%87%8f)

### 初始化数据表

运行命令:

    php bin/hyperf.php migrate

可初始化数据表, 具体请参考 [hyperf 迁移](https://hyperf.wiki/2.0/#/zh-cn/db/migration)

### 运行本地 console 端

执行命令:

    php bin/hyperf.php commune:start stdio_console -d -r

其中 ```-d``` 参数表示 debug 模式, ```-r```参数重置机器人的对话逻辑.


### 运行其它端


执行命令:

    php bin/hyperf.php commune:start

可查看目前开箱自带的端. 需要配合微信公众号/网页版/nlu 才能使用.


由于项目还在开发中, 尚未撰写相关文档.

如果想运行 demo, 建议还是先查看项目 https://github.com/thirdgerb/chatbot

如果有更多需要, 请加 qq群：907985715 直接向作者提出, 感谢!

