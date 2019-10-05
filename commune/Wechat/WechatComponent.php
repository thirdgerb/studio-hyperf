<?php


namespace Commune\Wechat;


use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Wechat\Providers\WechatAppServiceProvider;

/**
 * @property-read array $wechatConfig overtrue/easywechat 的基础配置.
 * @property-read string $serviceProvider 注册 Wechat 的 service provider.
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

            'serviceProvider' => WechatAppServiceProvider::class
        ];
    }


    protected function doBootstrap(): void
    {
        $this->app->registerConversationService($this->serviceProvider);
    }



}