<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Middleware;

use Lochmueller\LanguageDetection\Handler\Exception\AbstractHandlerException;
use Lochmueller\LanguageDetection\Handler\JsonDetectionHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JsonDetectionMiddleware implements MiddlewareInterface
{
    public function __construct(protected JsonDetectionHandler $jsonDetectionHandler) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->jsonDetectionHandler->handle($request);
        } catch (AbstractHandlerException) {
            return $handler->handle($request);
        }
    }
}
