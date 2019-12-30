<?php



$chatbot = include __DIR__ . '/demo.php';

// 替换掉系统默认的render

$chatbot['chatbotName'] = 'commune-dueros-demo';

$chatbot['logger']['path'] = BASE_PATH . '/runtime/logs/commune-dueros.log';

$chatbot['components'] = array_merge($chatbot['components'], [
    \Commune\Platform\DuerOS\DuerOSComponent::class => [
        'privateKey' => env('DUEROS_PRIVATE_KEY', ''),
        'sceneNames' => [
            'maze' => '方向迷宫',
            'story' => '大战长坂坡',
        ],
    ],
]);

return $chatbot;

