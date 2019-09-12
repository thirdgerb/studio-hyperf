<?php



$chatbot = include __DIR__ . '/chatbot.php';

// 替换掉系统默认的render
$chatbot['conversationProviders']['render'] = \Commune\DuerOS\Providers\RenderServiceProvider::class;


/**
 * 机器人的默认配置.
 *
 * @see \Commune\Chatbot\Config\ChatbotConfig
 */
return [
    'chatbotName' => 'commune-dueros',

    'components' => [
        \Commune\Demo\App\DemoComponent::class,
        \Commune\DuerOS\DuerOSComponent::class,
    ],
] + $chatbot;
