<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Middleware;

use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Lochmueller\LanguageDetection\Handler\JsonDetectionHandler;
use Lochmueller\LanguageDetection\Middleware\JsonDetectionMiddleware;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;

/**
 * @internal
 *
 * @coversNothing
 */
class JsonDetectionMiddlewareTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Middleware\JsonDetectionMiddleware
     */
    public function testMiddlewareWillExecuteJsonDetection(): void
    {
        $jsonDetectionHandler = $this->createStub(JsonDetectionHandler::class);
        $jsonDetectionHandler->method('handle')->willReturn(new NullResponse());

        $middleware = new JsonDetectionMiddleware($jsonDetectionHandler);
        $result = $middleware->process($this->createMock(ServerRequestInterface::class), $this->createMock(RequestHandlerInterface::class));

        self::assertInstanceOf(NullResponse::class, $result);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException
     * @covers \Lochmueller\LanguageDetection\Middleware\JsonDetectionMiddleware
     */
    public function testMiddlewareWillExecuteDefaultHandler(): void
    {
        $jsonDetectionHandler = $this->createStub(JsonDetectionHandler::class);
        $jsonDetectionHandler->method('handle')->willThrowException(new NoUserLanguagesException());

        $defaultHandler = $this->createStub(RequestHandlerInterface::class);
        $defaultHandler->method('handle')->willReturn(new NullResponse());

        $middleware = new JsonDetectionMiddleware($jsonDetectionHandler);
        $result = $middleware->process($this->createMock(ServerRequestInterface::class), $defaultHandler);

        self::assertInstanceOf(NullResponse::class, $result);
    }
}
