<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Middleware;

use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Handler\LanguageDetectionHandler;
use Lochmueller\LanguageDetection\Middleware\LanguageDetection;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;

/**
 * @internal
 * @coversNothing
 */
class LanguageDetectionTest extends AbstractTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Middleware\LanguageDetection
     */
    public function testMiddlewareWillExecuteLanguageDetection(): void
    {
        $languageDetectionHandler = $this->createStub(LanguageDetectionHandler::class);
        $languageDetectionHandler->method('handle')->willReturn(new NullResponse());

        $middleware = new LanguageDetection($languageDetectionHandler);
        $result = $middleware->process($this->createMock(ServerRequestInterface::class), $this->createMock(RequestHandlerInterface::class));

        self::assertInstanceOf(NullResponse::class, $result);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException
     * @covers \Lochmueller\LanguageDetection\Middleware\LanguageDetection
     */
    public function testMiddlewareWillExecuteDefaultHandler(): void
    {
        $languageDetectionHandler = $this->createStub(LanguageDetectionHandler::class);
        $languageDetectionHandler->method('handle')->willThrowException(new NoSelectedLanguageException());

        $defaultHandler = $this->createStub(RequestHandlerInterface::class);
        $defaultHandler->method('handle')->willReturn(new NullResponse());

        $middleware = new LanguageDetection($languageDetectionHandler);
        $result = $middleware->process($this->createMock(ServerRequestInterface::class), $defaultHandler);

        self::assertInstanceOf(NullResponse::class, $result);
    }
}
