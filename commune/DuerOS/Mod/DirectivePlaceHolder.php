<?php


namespace Commune\DuerOS\Mod;


use Baidu\Duer\Botsdk\Directive\BaseDirective;
use Commune\DuerOS\Messages\AbsDirective;

class DirectivePlaceHolder extends BaseDirective
{
    protected $data = [];

    public function __construct(AbsDirective $directive)
    {
        parent::__construct('');
        $this->data = $directive->toDirectiveArray();
    }

}