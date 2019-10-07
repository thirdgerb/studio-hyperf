<?php


namespace Commune\Platform\Wechat\Providers;


use Commune\Chatbot\Blueprint\Conversation\Conversation;
use Commune\Chatbot\Contracts\CacheAdapter;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Container\ContainerContract;
use Commune\Hyperf\Support\HttpBabel;
use EasyWeChat\OfficialAccount\Application as Wechat;
use Commune\Chatbot\Contracts\ClientFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Swoole\Http\Request as SwooleRequest;
use Commune\Platform\Wechat\WechatComponent;

/**
 * conversational 级别的绑定.
 */
class WechatAppServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = false;

    /**
     * @param ContainerContract $app
     */
    public function boot($app)
    {
    }

    public function register()
    {
        $this->app->singleton(Wechat::class, function($app){

            /**
             * @var WechatComponent $option
             * @var Conversation $app
             * @var ClientFactory $clientFactory
             * @var CacheAdapter $cache
             */
            $option = $app[WechatComponent::class];
            $config = $option->wechatConfig;

            $wechat = new Wechat($config);

            // rebind

            // request
            $request = $app[SwooleRequest::class];
            $symfonyRequest = HttpBabel::requestFromSwooleToSymfony($request);
            $wechat->rebind('request', $symfonyRequest);

            // logger
            $logger = $app[LoggerInterface::class];
            $wechat->rebind('log', $logger);
            $wechat->rebind('logger', $logger);

            // redis
            $cache = $app[CacheAdapter::class];
            $wechat->rebind('cache', $cache->getPSR16Cache());

            // client
            $clientFactory = $app[ClientFactory::class];
            $clientConfig = $wechat['config']->get('http', []);
            $client = $clientFactory->create($clientConfig);
            $wechat->rebind('http_client', $client);

            return $wechat;
        });

        $this->app->singleton(SymfonyRequest::class, function($app) {
            $request = $app[SwooleRequest::class];
            return HttpBabel::requestFromSwooleToSymfony($request);
        });

    }


}