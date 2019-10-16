<?php

namespace Commune\Platform\WebApi;


use Commune\Chatbot\Framework\Component\ComponentOption;
use Commune\Platform\WebApi\Demo\GetContextCode;
use Commune\Platform\WebApi\Demo\HelloWorld;
use Commune\Platform\WebApi\Libraries\AbstractAction;

/**
 * api 接口端. 可以用这种方式, 高性能地访问 session 的组件.
 *
 * @property-read array $getActions;
 * @property-read array $postActions;
 *
 */
class WebApiComponent extends ComponentOption
{
    const CODE_SUCCESS = 0;
    const CODE_BAD_REQUEST = 400;
    const CODE_INVALID_USER = 401;
    const CODE_FORBIDDEN = 403;
    const CODE_NOT_FOUND = 404;
    const CODE_BAD_METHOD = 404;
    const CODE_FAILURE = 500;


    public static function stub(): array
    {
        return [
            'getActions' => [
                'hello-world' => HelloWorld::class,
                'context-code' => GetContextCode::class,
            ],
            'postActions' => [

            ]

        ];
    }

    protected function doBootstrap(): void
    {
    }


    public static function validate(array $data): ? string
    {
        $actions = array_merge($data['getActions'] ?? [], $data['postActions'] ?? []);

        foreach ($actions as $action) {
            if (!is_a($action, AbstractAction::class, TRUE)) {
                return "invalid action $action, only accept subclass of ". AbstractAction::class;
            }

        }
        return parent::validate($data);
    }


}