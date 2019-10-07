<?php

/**
 * Class Ordinal
 * @package Commune\Platform\DuerOS\Messages\Directives
 */

namespace Commune\Platform\DuerOS\Messages\Directives;


use Commune\Platform\DuerOS\Constants\Directives;
use Commune\Platform\DuerOS\Constants\DuerOSIntent;
use Commune\Platform\DuerOS\Messages\AbsDirective;

class SelectIntentDirective extends AbsDirective
{
    /**
     * @var array
     */
    protected $suggestions;

    /**
     * @var string[]
     */
    protected $intents;

    /**
     * SelectIntentDirective constructor.
     * @param array $suggestions
     * @param string[] $intents
     */
    public function __construct(array $suggestions, array $intents)
    {
        $this->suggestions = $suggestions;
        $this->intents = $intents;
        parent::__construct();
    }


    /**
     * @return array
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    public function getType(): string
    {
        return Directives::DIALOG_SELECT_INTENT;
    }

    protected function parseOptions() : array
    {
        $options = [];
        $i = 0;
        foreach ($this->suggestions as $index => $suggestion) {
            $i++;

            if (array_key_exists($index, $this->intents)) {
                $options[] = $this->wrapOption($i, $index, $suggestion, $this->intents[$index]);
            }

        }

        return $options;
    }

    public function toDirectiveArray(): array
    {
        return [
            'type' => $this->getType(),
            'options' => $this->parseOptions()
        ];
    }

    protected function wrapOption(int $ordinal, $value, $intent) : array
    {
        $option = [
            'type' => Directives::OPTION_TYPE_KEYWORD,
            'value' => strval($value),
            'index' => $ordinal,
            'updatedIntent' => [
                'name' => $intent,
                'confirmationStatus' => DuerOSIntent::STATUS_NONE,
                'slots' => [
                ]
            ],
        ];
        return $option;
    }


}