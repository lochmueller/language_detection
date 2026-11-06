<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Middleware;

use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Lochmueller\LanguageDetection\Handler\JsonDetectionHandler;
use Lochmueller\LanguageDetection\Middleware\JsonDetectionMiddleware;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Middleware\JsonDetectionMiddleware::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException::class)]
class JsonDetectionMiddlewareTest extends AbstractUnitTest
{
    public function testMiddlewareWillExecuteJsonDetection(): void
    {
        $jsonDetectionHandler = self::createStub(JsonDetectionHandler::class);
        $jsonDetectionHandler->method('handle')->willReturn(new NullResponse());

        $middleware = new JsonDetectionMiddleware($jsonDetectionHandler);
        $result = $middleware->process(self::createStub(ServerRequestInterface::class), self::createStub(RequestHandlerInterface::class));

        self::assertInstanceOf(NullResponse::class, $result);
    }

    public function testMiddlewareWillExecuteDefaultHandler(): void
    {
        $jsonDetectionHandler = self::createStub(JsonDetectionHandler::class);
        $jsonDetectionHandler->method('handle')->willThrowException(new NoUserLanguagesException());

        $defaultHandler = self::createStub(RequestHandlerInterface::class);
        $defaultHandler->method('handle')->willReturn(new NullResponse());

        $middleware = new JsonDetectionMiddleware($jsonDetectionHandler);
        $result = $middleware->process(self::createStub(ServerRequestInterface::class), $defaultHandler);

        self::assertInstanceOf(NullResponse::class, $result);
    }
}
