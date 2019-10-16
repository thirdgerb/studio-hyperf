<?php


namespace Commune\Platform\WebApi\Libraries;


class ApiResult
{

    /**
     * @var int
     */
    public $code;

    /**
     * @var string
     */
    public $msg;

    /**
     * @var array
     */
    public $data;

    /**
     * ApiResult constructor.
     * @param array $data
     * @param int $code
     * @param string $msg
     */
    public function __construct(
        array $data = [],
        int $code = 0,
        string $msg = 'success'
    )
    {
        $this->code = $code;
        $this->msg = $msg;
        $this->data = $data;
    }


}