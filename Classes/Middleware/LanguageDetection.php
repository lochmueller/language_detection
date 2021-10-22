<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Middleware;

use LD\LanguageDetection\Handler\Exception\AbstractHandlerException;
use LD\LanguageDetection\Handler\LanguageDetectionHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * LanguageDetection.
 */
class LanguageDetection implements MiddlewareInterface
{
    protected LanguageDetectionHandler $languageDetectionHandler;

    public function __construct(LanguageDetectionHandler $languageDetectionHandler)
    {
        $this->languageDetectionHandler = $languageDetectionHandler;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->languageDetectionHandler->handle($request);
        } catch (AbstractHandlerException $exception) {
            return $handler->handle($request);
        }
    }
}
