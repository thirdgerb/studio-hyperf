<?php



$chatbot = include __DIR__ . '/demo.php';

// 替换掉系统默认的render

$chatbot['chatbotName'] = 'dueros_maze';
$chatbot['conversationProviders']['render'] = \Commune\DuerOS\Providers\RenderServiceProvider::class;

$chatbot['logger']['path'] = BASE_PATH . '/runtime/logs/dueros_story.log';

$chatbot['components'] = array_merge($chatbot['components'], [
    \Commune\DuerOS\DuerOSComponent::class => [
        'name' => '长坂坡',
    ],
    \Commune\Components\StoryComponent::class,
]);

$chatbot['host']['rootContextName'] = 'story.examples.sanguo.changbanpo';

/**
 * 默认的迷宫demo
 */
return $chatbot;

