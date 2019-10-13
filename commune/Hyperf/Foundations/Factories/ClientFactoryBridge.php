<?php


namespace Commune\Hyperf\Foundations\Factories;

use Commune\Chatbot\Contracts\ClientFactory;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Hyperf\Guzzle\ClientFactory as HyperfFactory;

class ClientFactoryBridge implements ClientFactory
{
    /**
     * @var HyperfFactory
     */
    protected $hyperfFactory;

    /**
     * ClientFactoryBridge constructor.
     * @param HyperfFactory $hyperfFactory
     */
    public function __construct(HyperfFactory $hyperfFactory)
    {
        $this->hyperfFactory = $hyperfFactory;
    }

    public function create(array $config): Client
    {
        $config[RequestOptions::TIMEOUT] = $config[RequestOptions::TIMEOUT] ?? 0.5;
        return $this->hyperfFactory->create($config);
    }


}