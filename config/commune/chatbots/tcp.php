<?php

$chatbot = include __DIR__ . '/demo.php';


$chatbot['chatbotName'] = 'commune-tcp-demo';
$chatbot['logger']['path'] = BASE_PATH . '/runtime/logs/commune-tcp.log';
$chatbot['configBindings'][] = \Commune\Hyperf\Servers\Tcp\TcpOption::class;

return $chatbot;
