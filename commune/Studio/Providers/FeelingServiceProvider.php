<?php


namespace Commune\Studio\Providers;


use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;
use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;
use Commune\Chatbot\OOHost\Emotion\Feeling;

/**
 * nlu emotion configuration
 *
 * todo need better way 需要更合适的方案
 */
class FeelingServiceProvider extends BaseServiceProvider
{
    const IS_PROCESS_SERVICE_PROVIDER = true;


    /**
     * mapping intent to emotion
     *
     * @var array
     */
    protected $intentMap = [
        Positive::class => [
            // intentName

        ],
        Negative::class => [

        ]
    ];

    protected function registerExperience(Feeling $feels) : void
    {
        //$feels->experience(Positive::class, function(){
        //
        //});
    }


    /**
     * @param \Commune\Container\ContainerContract $app
     */
    public function boot($app)
    {
        /**
         * @var Feeling $feels
         */
        $feels = $app->make(Feeling::class);

        foreach ($this->intentMap as $emotion => $intents) {
            $feels->setIntentMap($emotion, $intents);
        }

        $this->registerExperience($feels);
    }

    public function register()
    {
    }


}