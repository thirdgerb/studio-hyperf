<?php


namespace Commune\Wechat;


use Commune\Chatbot\Framework\Component\ComponentOption;

/**
 * @property-read array $wechatConfig overtrue/easywechat 的基础配置.
 */
class WechatComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'wechatConfig' => [
                /**
                 * 账号基本信息，请从微信公众平台/开放平台获取
                 */
                'app_id'  => 'your-app-id',         // AppID
                'secret'  => 'your-app-secret',     // AppSecret
                'token'   => 'your-token',          // Token
                'aes_key' => '',                    // EncodingAESKey，兼容与安全模式下请一定要填写！！！

            ],
        ];
    }


    protected function doBootstrap(): void
    {
    }



}