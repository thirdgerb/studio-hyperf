<?php

/**
 * Class DuerOSNLUParser
 * @package Commune\DuerOS\Servers
 */

namespace Commune\DuerOS\Servers;

use Baidu\Duer\Botsdk\Request as DuerRequest;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Chatbot\App\Components\Predefined\Navigation\HomeInt;
use Commune\Chatbot\App\Components\Predefined\Navigation\QuitInt;
use Commune\Chatbot\Framework\Conversation\NatureLanguageUnit;
use Commune\DuerOS\Constants\DuerOSCommonIntents;

class DuerOSNLUParser
{

    /**
     * @var DuerRequest
     */
    protected $duerRequest;

    /**
     * @var NLU
     */
    protected $nlu;

    /**
     * DuerOSNLUParser constructor.
     * @param DuerRequest $duerRequest
     */
    public function __construct(DuerRequest $duerRequest)
    {
        $this->duerRequest = $duerRequest;
    }

    public function parseNLU() : NLU
    {
        $nlu = new NatureLanguageUnit();

        $userId = $this->duerRequest->getUserId();
        if (empty($userId)) {
            $nlu->setMatchedIntent(QuitInt::getContextName());

        } elseif ($this->duerRequest->isLaunchRequest()) {
            $nlu->setMatchedIntent(HomeInt::class);

        } elseif ($this->duerRequest->isSessionEndedRequest()) {
            $nlu->setMatchedIntent(QuitInt::class);

        } else {
            $nlu = $this->bootIntentNLU($nlu);
        }

        return $this->nlu = $nlu;

    }


    protected function bootIntentNLU(NLU $nlu) : NLU
    {
        $botNLU = $this->duerRequest->getNlu();

        // matched nlu
        $matchedIntent = $botNLU->getIntentName();
        if (empty($matchedIntent)) {
            return $nlu;
        }

        // 默认模式下, 视作没有匹配到任何意图.
        if ($matchedIntent === DuerOSCommonIntents::COMMON_DEFAULT) {
            return $nlu;
        }

        // 默认意图.
        $communeMatchedIntent = $this->parseDuerOSIntentToCommune($matchedIntent);
        $nlu->setMatchedIntent($communeMatchedIntent);
        $intentData = $this->duerRequest->getData()['request']['intents'];

        // possible intent and entities
        foreach ($intentData as $intentDatum) {
            $duerOSIntentName = $intentDatum['name'] ?? '';
            if (empty($duerOSIntentName)) {
                continue;
            }

            $name = $this->parseDuerOSIntentToCommune($duerOSIntentName);

            $nlu->addPossibleIntent($name, 0);
            $slots = $intentDatum['slots'] ?? [];
            if (!empty($slots)) {
                $entities = array_map(function($slot){
                    return $slot['values'] ?? $slot['value'] ?? null;
                }, $slots);
                $nlu->setIntentEntities($name, $entities);
            }
        }
        return $nlu;
    }

    protected function parseDuerOSIntentToCommune(string $duerOSIntentName) : string
    {

    }
}