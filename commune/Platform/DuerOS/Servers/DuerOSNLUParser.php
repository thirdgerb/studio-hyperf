<?php

/**
 * Class DuerOSNLUParser
 * @package Commune\Platform\DuerOS\Servers
 */

namespace Commune\Platform\DuerOS\Servers;

use Baidu\Duer\Botsdk\Request as DuerRequest;
use Commune\Chatbot\Blueprint\Conversation\NLU;
use Commune\Components\Predefined\Intents\Navigation\HomeInt;
use Commune\Components\Predefined\Intents\Navigation\QuitInt;
use Commune\Chatbot\Framework\Conversation\NatureLanguageUnit;
use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Platform\DuerOS\Constants\CommonIntents;
use Commune\Platform\DuerOS\Constants\DuerOSIntent;
use Commune\Platform\DuerOS\DuerOSComponent;

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
     * @var DuerOSComponent
     */
    protected $duerOSComponent;

    /**
     * DuerOSNLUParser constructor.
     * @param DuerRequest $duerRequest
     * @param DuerOSComponent $component
     */
    public function __construct(DuerRequest $duerRequest, DuerOSComponent $component)
    {
        $this->duerRequest = $duerRequest;
        $this->duerOSComponent = $component;
    }

    public function parseNLU() : NLU
    {
        $nlu = new NatureLanguageUnit();

        if ($this->duerRequest->isLaunchRequest()) {
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
        if ($matchedIntent === CommonIntents::COMMON_DEFAULT) {
            return $nlu;
        }

        // 意图转换.
        $communeMatchedIntent = $this->parseDuerOSIntentToCommune($matchedIntent);
        if (empty($communeMatchedIntent)) {
            return $nlu;
        }

        // 以上都是没有匹配到任何意图.

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

            // entities 赋值.
            $entities = [];

            // confirmed 赋值.
            $intentConfirmed = $intentDatum[DuerOSIntent::CONFIRMATION_STATUS] ?? null;
            if ($intentConfirmed === DuerOSIntent::STATUS_CONFIRMED) {
                $entities[IntentMessage::INTENT_CONFIRMATION] = true;
            } elseif ($intentConfirmed === DuerOSIntent::STATUS_DENIED) {
                $entities[IntentMessage::INTENT_CONFIRMATION] = false;
            }

            // slots 赋值.
            $slots = $intentDatum['slots'] ?? [];
            $confirmedSlots = [];
            if (!empty($slots)) {

                foreach ($slots as $slotName => $slotValue) {

                    $entityName = $this->parseEntityName(
                        $duerOSIntentName,
                        $slotValue['name'] ?? $slotName
                    );

                    $entities[$entityName] = $slotValue['values']
                        ?? $slotValue['name']
                        ?? null;

                    // confirmation
                    if (isset($slotValue[DuerOSIntent::CONFIRMATION_STATUS])) {
                        $confirmedSlots[$entityName] = $slotValue[DuerOSIntent::CONFIRMATION_STATUS] == DuerOSIntent::STATUS_CONFIRMED;
                    }

                }
            }

            if (!empty($confirmedSlots)) {
                $entities[IntentMessage::ENTITIES_CONFIRMATION] = $confirmedSlots;
            }

            if (!empty($entities)) {
                $nlu->setIntentEntities($name, $entities);
            }
        }

        // 标记 NLU 已经处理过.
        $nlu->done(static::class);
        return $nlu;
    }

    protected function parseDuerOSIntentToCommune(string $duerOSIntentName) : ? string
    {
        $mapping = $this->duerOSComponent->intentMapping ?? [];
        return $mapping[$duerOSIntentName] ?? $duerOSIntentName;
    }

    protected function parseEntityName(string $intentName, string $duerEntityName) : string
    {
        $mapping = $this->duerOSComponent->entityMapping ?? [];
        $parsing = $mapping[$intentName] ?? [];
        return $parsing[$duerEntityName] ?? $duerEntityName;
    }
}