## 在 Hyperf 中启用平台的默认配置.


- TcpGhostPlatformConfig
    - desc: 基于 TCP 实现的 Ghost 端.
- StdioShellPlatformConfig
    - desc: 基于 Stdio 库实现的双工 shell 端.
    - feats:
        - 联通 Tcp Ghost 端.
        - 异步消息实时呈现还有问题. 
- StdioConsolePlatform
    - desc: 基于 stdio 库实现的命令行平台.
    - feats
        - 通常供调试使用.
        - 同步平台, 无法接受异步消息.
        - 数据库/缓存/通信 都是 demo 模拟的.