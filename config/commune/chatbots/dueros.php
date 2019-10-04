<?php



$chatbot = include __DIR__ . '/demo.php';

// 替换掉系统默认的render

$chatbot['chatbotName'] = 'commune-dueros-demo';

$chatbot['logger']['path'] = BASE_PATH . '/runtime/logs/commune-dueros.log';

$chatbot['components'] = array_merge($chatbot['components'], [
    \Commune\Hyperf\Demo\HyperfDemoComponent::class,
    \Commune\DuerOS\DuerOSComponent::class => [
        'privateKey' => env('DUEROS_PRIVATE_KEY', ''),
    ],
    \Commune\Components\StoryComponent::class,
]);

$chatbot['host']['rootContextName'] = \Commune\Hyperf\Demo\Contexts\TestCase::getContextName();

$chatbot['host']['sceneContextNames'] = [
    'maze' => \Commune\Demo\App\Cases\Maze\MazeInt::getContextName(),
    'story' => 'story.examples.sanguo.changbanpo',
];

return $chatbot;

