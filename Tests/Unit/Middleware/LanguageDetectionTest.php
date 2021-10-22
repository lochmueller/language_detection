<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Middleware;

use LD\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use LD\LanguageDetection\Handler\LanguageDetectionHandler;
use LD\LanguageDetection\Middleware\LanguageDetection;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
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
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
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
     * @covers \LD\LanguageDetection\Middleware\LanguageDetection
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
