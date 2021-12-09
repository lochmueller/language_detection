<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Middleware;

use Lochmueller\LanguageDetection\Handler\JsonDetectionHandler;
use Lochmueller\LanguageDetection\Middleware\JsonDetectionMiddleware;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;

/**
 * @internal
 * @coversNothing
 */
class JsonDetectionMiddlewareTest extends AbstractTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Middleware\JsonDetectionMiddleware
     */
    public function testMiddlewareWillExecuteLanguageDetection(): void
    {
        $jsonDetectionHandler = $this->createStub(JsonDetectionHandler::class);
        $jsonDetectionHandler->method('handle')->willReturn(new NullResponse());

        $middleware = new JsonDetectionMiddleware($jsonDetectionHandler);
        $result = $middleware->process($this->createMock(ServerRequestInterface::class), $this->createMock(RequestHandlerInterface::class));

        self::assertInstanceOf(NullResponse::class, $result);
    }
}
