<?php


namespace Commune\Platform\Web\Providers;


use Commune\Chatbot\Blueprint\ServiceProvider;
use Commune\Container\ContainerContract;
use Commune\Platform\Web\Contracts\ResponseRender;

class ResponseServiceProvider extends ServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = false;

    protected $concrete;

    public function __construct(ContainerContract $app, string $apiRenderConcrete)
    {
        $this->concrete = $apiRenderConcrete;
        parent::__construct($app);
    }

    public function boot($app)
    {
    }

    public function register()
    {
        $this->app->singleton(ResponseRender::class, $this->concrete);
    }


}