<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Middleware;

use Lochmueller\LanguageDetection\Handler\Exception\AbstractHandlerException;
use Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LanguageDetectionMiddleware implements MiddlewareInterface
{
    public function __construct(protected LanguageDetectionHandler $languageDetectionHandler) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->languageDetectionHandler->handle($request);
        } catch (AbstractHandlerException $exception) {
            return $handler->handle($request);
        }
    }
}
