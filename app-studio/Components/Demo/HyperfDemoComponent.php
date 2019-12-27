<?php

namespace Commune\Studio\Components\Demo;


use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Components\Demo\DemoComponent;

/**
 * @property-read string $langPath
 */
class HyperfDemoComponent extends ComponentOption
{

    public static function stub(): array
    {
        return [
            'langPath' => __DIR__ . '/resources/trans',
        ];
    }



    protected function doBootstrap(): void
    {
        $path = realpath($this->langPath);

        if (!empty($path)) {
            $this->loadTranslationResource($path);
        }

        $this->loadSelfRegisterByPsr4(
            "Commune\\Hyperf\\Demo\\Contexts\\",
            __DIR__ . '/Contexts/'
        );

        $this->dependComponent(DemoComponent::class);
    }


}