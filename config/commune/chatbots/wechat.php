<?php



$chatbot = include __DIR__ . '/demo.php';

// 替换掉系统默认的render

$chatbot['chatbotName'] = 'commune-wechat-demo';

$chatbot['logger']['path'] = BASE_PATH . '/runtime/logs/commune-wechat.log';

$chatbot['components'] = array_merge($chatbot['components'], [
    // 微信公众号的配置
    \Commune\Platform\Wechat\WechatComponent::class => [
        'wechatConfig' => [
            'app_id'  => env('WECHAT_APP_ID', ''),
            'secret'  => env('WECHAT_APP_SECRET', ''),
            'token'   => env('WECHAT_TOKEN', ''),
            'aes_key' => env('WECHAT_AES_KEY', ''),
        ],
    ],
]);

return $chatbot;

