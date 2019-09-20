<?php


namespace Commune\Components\Story\Providers;


use Commune\Chatbot\Blueprint\Application;
use Commune\Chatbot\Framework\Exceptions\ConfigureException;
use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Context\ContextRegistrar;
use Commune\Components\Story\Basic\StoryRegistrar;
use Commune\Components\Story\Basic\StoryRegistrarImpl;
use Commune\Components\Story\Options\ScriptOption;
use Commune\Components\StoryComponent;
use Commune\Container\ContainerContract;
use Symfony\Component\Yaml\Yaml;

class StoryServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;

    /**
     * @var StoryComponent
     */
    protected $storyOption;

    /**
     * StoryServiceProvider constructor.
     * @param ContainerContract $app
     * @param StoryComponent $storyOption
     */
    public function __construct(ContainerContract $app, StoryComponent $storyOption)
    {
        $this->storyOption = $storyOption;
        parent::__construct($app);
    }


    public function boot($app)
    {
        /**
         * @var StoryRegistrar $registrar
         */
        $registrar = $app[StoryRegistrar::class];

        foreach ($this->storyOption->resources as $resource) {

            if (!file_exists($resource)) {
                throw new ConfigureException(
                    static::class
                    . " story resource $resource not found"
                );
            }
            $content = file_get_contents($resource);
            $config = Yaml::parse($content);
            $scriptOption = new ScriptOption($config);
            $registrar->registerScriptOption($scriptOption);
        }
    }

    public function register()
    {

        // 注册 story 服务.
        $this->app->singleton(StoryRegistrar::class, function($app){
            $parent = $app[ContextRegistrar::class];
            $chatApp = $app[Application::class];
            return new StoryRegistrarImpl($chatApp, $parent);
        });
    }


}