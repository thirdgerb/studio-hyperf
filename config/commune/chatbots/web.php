<?php



$chatbot = include __DIR__ . '/demo.php';

// 替换掉系统默认的render

$chatbot['chatbotName'] = 'commune-web-demo';

$chatbot['logger']['path'] = BASE_PATH . '/runtime/logs/commune-web.log';
return $chatbot;

