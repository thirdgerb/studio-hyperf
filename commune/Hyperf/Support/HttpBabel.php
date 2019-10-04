<?php


namespace Commune\Hyperf\Support;

use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Hyperf\HttpMessage\Server\Request as HyperfRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Psr\Http\Message\ServerRequestInterface as Psr7Request;


class HttpBabel
{

    public static function requestFromSwooleToHyperf(SwooleRequest $request) : HyperfRequest
    {
        return HyperfRequest::loadFromSwooleRequest($request);
    }

    public static function requestFromSwooleToPSR(SwooleRequest $request) : Psr7Request
    {
        return static::requestFromSwooleToHyperf($request);
    }

    public static function requestFromSwooleToSymfony(SwooleRequest $swRequest) : SymfonyRequest
    {
        $query = $swRequest->get ?? [];
        $request = $swRequest->post ?? [];
        $cookie = $swRequest->cookie ?? [];
        $files = $swRequest->files ?? [];
        $content = $swRequest->rawContent() ?: null;

        $server = array_change_key_case($swRequest->server, CASE_UPPER);
        foreach ($swRequest->header as $key => $val) {
            $server[sprintf('HTTP_%s', strtoupper(str_replace('-', '_', $key)))] = $val;
        }

        return new SymfonyRequest(
            $query,
            $request,
            [],
            $cookie,
            $files,
            $server,
            $content
        );
    }


    public static function sendResponseFromSymfonyToSwoole(
        SymfonyResponse $symfonyRes,
        SwooleResponse $swooleRes,
        bool $end = true
    ) : void
    {
        // status
        $swooleRes->status($symfonyRes->getStatusCode());
        // headers
        foreach ($symfonyRes->headers->allPreserveCaseWithoutCookies() as $name => $values) {
            $value = implode(';', $values);
            var_dump('value', $value);
            $swooleRes->header($name, $value);
        }

        // cookies
        foreach ($symfonyRes->headers->getCookies() as $cookie) {
            $swooleRes->cookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                $cookie->isHttpOnly()
            );
        }

        // response content
        $content = $symfonyRes->getContent();
        $content = is_string($content) ? $content : '';
        if ($end) {
            $swooleRes->end($content);
        } else {
            $swooleRes->write($content);
        }
    }

}