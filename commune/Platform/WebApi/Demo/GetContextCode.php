<?php


namespace Commune\Platform\WebApi\Demo;


use Commune\Chatbot\OOHost\Session\Session;
use Commune\Platform\WebApi\Libraries\AbstractAction;
use Commune\Platform\WebApi\Libraries\ApiResult;
use Commune\Platform\WebApi\WebApiComponent;

class GetContextCode extends AbstractAction
{
    public function validateInput(array $input): ? string
    {
        if (empty($input['contextName'])) {
            return  'contextName is required';
        }
        return null;
    }

    public function doHandle(Session $session, array $input): ApiResult
    {
        $name = $input['contextName'] ?? '';

        if ($session->contextRepo->hasDef($name)) {
            $def = $session->contextRepo->getDef($name);
            $clazz = $def->getClazz();

            $r = new \ReflectionClass($clazz);
            $file = $r->getFileName();

            $content = file_get_contents($file);

            return new ApiResult([
                'name' => $name,
                'class' => $def->getClazz(),
                'desc' => $def->getDesc(),
                'code' => $content
            ]);
        }

        return new ApiResult([], WebApiComponent::CODE_NOT_FOUND, "context $name not found");
    }


}