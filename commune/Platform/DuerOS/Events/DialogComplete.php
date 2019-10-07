<?php

/**
 * Class ConversationRound
 * @package Commune\Platform\DuerOS\Events
 */

namespace Commune\Platform\DuerOS\Events;


/**
 * 完成一轮对话的事件.
 * 记录输入输出信息. 可以用于监听.
 */
class DialogComplete
{
    /**
     * @var string
     */
    public $traceId;

    /**
     * @var string
     */
    public $duerRequest;

    /**
     * @var string
     */
    public $duerResponse;

    /**
     * DialogComplete constructor.
     * @param string $traceId
     * @param string $duerRequest
     * @param string $duerResponse
     */
    public function __construct(string $traceId, string $duerRequest, string $duerResponse)
    {
        $this->traceId = $traceId;
        $this->duerRequest = $duerRequest;
        $this->duerResponse = $duerResponse;
    }


}