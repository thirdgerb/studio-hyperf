<?php

/**
 * Class SelectSlotDirective
 * @package Commune\Platform\DuerOS\Messages\Directives
 */

namespace Commune\Platform\DuerOS\Messages\Directives;


use Commune\Chatbot\OOHost\Context\Intent\IntentMessage;
use Commune\Platform\DuerOS\Constants\Directives;
use Commune\Platform\DuerOS\Constants\DuerOSIntent;
use Commune\Platform\DuerOS\Messages\AbsDirective;

class SelectSlotDirective extends AbsDirective
{
    /**
     * @var IntentMessage
     */
    protected $intent;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var array
     */
    protected $suggestions;

    /**
     * SelectSlotDirective constructor.
     * @param IntentMessage $intent
     * @param string $entityName
     * @param array $suggestions
     */
    public function __construct(IntentMessage $intent, string $entityName, array $suggestions)
    {
        $this->intent = $intent;
        $this->entityName = $entityName;
        $this->suggestions = $suggestions;
        parent::__construct();
    }


    public function getType(): string
    {
        return Directives::DIALOG_SELECT_SLOT;
    }

    public function toDirectiveArray(): array
    {
        return [
            'type' => $this->getType(),
            'slotToSelect' => $this->entityName,
            'updatedIntent' => $this->getUpdatedIntent(),
            'options' => $this->wrapOptions(),
        ];
    }

    protected function wrapOptions() : array
    {
        $options = [];
        $i = 0;
        foreach ($this->suggestions as $index => $value) {
            $i++;
            $options[] = $this->wrapOption($i, $value);
        }
        return $options;
    }

    protected function wrapOption(int $index, $value) : array
    {
        return [
            'type' => Directives::OPTION_TYPE_KEYWORD,
            'value' => strval($value),
            'entity' => $this->entityName,
            'index' => $index
        ];
    }

    protected function getUpdatedIntent() : array
    {
        $data =  [
            'name' => $this->intent->getName(),
            'confirmationStatus' => $this->intent->isConfirmed,
        ];

        $slots = [];
        $entityNames = $this->intent->getDef()->getEntityNames();

        if (!empty($entityNames)) {
            $slots = $this->wrapSlots( $slots, $entityNames);
        }

        $data['slots'] = $slots;
        return $data;
    }

    protected function wrapSlots(array $entityNames, array $slots) : array
    {
        foreach ($entityNames as $name) {
            $value = $this->intent->__get($name);

            if (isset($value) && is_array($value) || is_scalar($value)) {
                if (is_scalar($value)) {
                    $value = [$value];
                }

                $value = array_filter($value, function($i){
                    return is_scalar($i);
                });

                $status = $this->intent->confirmedEntities[$name] ?? null;

                $statusText = is_null($status)
                    ? DuerOSIntent::STATUS_NONE
                    : (
                    $status
                        ? DuerOSIntent::STATUS_CONFIRMED
                        : DuerOSIntent::STATUS_DENIED
                    );

                $slots[$name] = [
                    'name' => $name,
                    'values' => $value,
                    'confirmationStatus' => $statusText
                ];
            }
        }
        return $slots;
    }



}