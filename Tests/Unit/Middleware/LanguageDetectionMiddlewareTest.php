<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Middleware;

use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler;
use Lochmueller\LanguageDetection\Middleware\LanguageDetectionMiddleware;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Middleware\LanguageDetectionMiddleware::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException::class)]
class LanguageDetectionMiddlewareTest extends AbstractUnitTest
{
    public function testMiddlewareWillExecuteLanguageDetection(): void
    {
        $languageDetectionHandler = self::createStub(LanguageDetectionHandler::class);
        $languageDetectionHandler->method('handle')->willReturn(new NullResponse());

        $middleware = new LanguageDetectionMiddleware($languageDetectionHandler);
        $result = $middleware->process(self::createStub(ServerRequestInterface::class), self::createStub(RequestHandlerInterface::class));

        self::assertInstanceOf(NullResponse::class, $result);
    }

    public function testMiddlewareWillExecuteDefaultHandler(): void
    {
        $languageDetectionHandler = self::createStub(LanguageDetectionHandler::class);
        $languageDetectionHandler->method('handle')->willThrowException(new NoSelectedLanguageException());

        $defaultHandler = self::createStub(RequestHandlerInterface::class);
        $defaultHandler->method('handle')->willReturn(new NullResponse());

        $middleware = new LanguageDetectionMiddleware($languageDetectionHandler);
        $result = $middleware->process(self::createStub(ServerRequestInterface::class), $defaultHandler);

        self::assertInstanceOf(NullResponse::class, $result);
    }
}
