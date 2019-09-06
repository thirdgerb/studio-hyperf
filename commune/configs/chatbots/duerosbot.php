<?php



$chatbot = include __DIR__ . '/chatbot.php';
$chatbot['conversationProviders']['render'] = \Commune\DuerOS\Providers\RenderServiceProvider::class;

/**
 * 机器人的默认配置.
 *
 * @see \Commune\Chatbot\Config\ChatbotConfig
 */
return [
    'chatbotName' => 'commune-dueros',

    'configBindings' => [
        \Commune\DuerOS\Options\DuerOSOption::class,
    ],
] + $chatbot;
