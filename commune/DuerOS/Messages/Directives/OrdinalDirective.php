<?php

/**
 * Class Ordinal
 * @package Commune\DuerOS\Messages\Directives
 */

namespace Commune\DuerOS\Messages\Directives;


use Commune\Chatbot\App\Components\Predefined\Dialogue\OrdinalInt;
use Commune\DuerOS\Constants\Directives;
use Commune\DuerOS\Constants\DuerOSIntent;
use Commune\DuerOS\Messages\AbsDirective;

class OrdinalDirective extends AbsDirective
{
    /**
     * @var array
     */
    protected $suggestions;

    /**
     * @var string|null
     */
    protected $entityName;

    /**
     * OrdinalDirective constructor.
     * @param array $suggestions
     * @param string|null $entityName
     */
    public function __construct(array $suggestions, string $entityName = null)
    {
        $this->suggestions = $suggestions;
        $this->entityName = $entityName;
        parent::__construct();
    }

    /**
     * @return array
     */
    public function getSuggestions(): array
    {
        return $this->suggestions;
    }

    /**
     * @return string
     */
    public function getEntityName(): string
    {
        return $this->entityName;
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

            if (is_string($index)) {
                $options[] = $this->wrapOption($i, $index);
            }
            $options[] = $this->wrapOption($i, $suggestion);
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

    /**
     * @param int $ordinal
     * @param int|string $suggestion
     * @return array
     */
    protected function wrapOption(int $ordinal, $suggestion) : array
    {
        $option = [
            'type' => Directives::OPTION_TYPE_KEYWORD,
            'value' => strval($suggestion),
            'index' => $ordinal,
            'updatedIntent' => [
                'name' => OrdinalInt::getContextName(),
                'confirmationStatus' => DuerOSIntent::STATUS_NONE,
                'slots' => [
                    OrdinalInt::ORDINAL_VAR => [
                        'name' => OrdinalInt::ORDINAL_VAR,
                        'values' => [ strval($ordinal)],
                        'confirmationStatus' => DuerOSIntent::STATUS_NONE,
                    ]
                ]
            ],
        ];

        if (isset($this->entityName)) {
            $option['entity'] = $this->entityName;
        }

        return $option;
    }


}