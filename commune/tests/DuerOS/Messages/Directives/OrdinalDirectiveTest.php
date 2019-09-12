<?php

/**
 * Class OrdinalDirectiveTest
 * @package Commune\Test\Studio\DuerOS\Messages\Directives
 */

namespace Commune\Test\Studio\DuerOS\Messages\Directives;


use Commune\DuerOS\Messages\Directives\OrdinalDirective;
use PHPUnit\Framework\TestCase;

class OrdinalDirectiveTest extends TestCase
{

    public function testSuggestions()
    {
        $ordinal = new OrdinalDirective([
            'a' => '123',
            'b' => '456',
        ], 'test');


        dd($ordinal->toDirectiveArray());

    }

}