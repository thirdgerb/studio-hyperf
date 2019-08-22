<?php


namespace Commune\Studio\Providers;


use Commune\Chatbot\Framework\Providers\BaseServiceProvider;
use Commune\Chatbot\OOHost\Emotion\Emotions\Negative;
use Commune\Chatbot\OOHost\Emotion\Emotions\Positive;
use Commune\Chatbot\OOHost\Emotion\Feeling;

/**
 * nlu emotion configuration
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

    protected function experience(Feeling $feels) : void
    {
        //$feels->experience(Positive::class, function(){
        //
        //});
    }


    public function boot($app)
    {
        /**
         * @var Feeling $feels
         */
        $feels = $app->make(Feeling::class);

        foreach ($this->intentMap as $emotion => $intents) {
            $feels->setIntentMap($emotion, $intents);
        }

        $this->experience($feels);
    }

    public function register()
    {
    }


}