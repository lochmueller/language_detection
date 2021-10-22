<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Handler\Exception;

use LD\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use LD\LanguageDetection\Tests\Unit\AbstractTest;

/**
 * @internal
 * @coversNothing
 */
class DisableLanguageDetectionExceptionTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Handler\Exception\DisableLanguageDetectionException
     */
    public function testIfExceptionHasStaticMessageAndCode(): void
    {
        $exception = new DisableLanguageDetectionException();
        self::assertNotEquals('', $exception->getMessage());
        self::assertNotEquals(0, $exception->getCode());
    }
}
