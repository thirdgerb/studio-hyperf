<?php


namespace Commune\Platform\Web\Contracts;


use Commune\Chatbot\OOHost\Dialogue\Dialog;

interface ResponseRender
{

    public function receiveMessages(array $messages) : void;

    public function receiveDialog(Dialog $dialog) : void;

    public function renderOutput() : array;
}