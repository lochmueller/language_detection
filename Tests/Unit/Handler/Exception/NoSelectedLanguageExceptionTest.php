<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Handler\Exception;

use LD\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use LD\LanguageDetection\Tests\Unit\AbstractTest;

/**
 * @internal
 * @coversNothing
 */
class NoSelectedLanguageExceptionTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Handler\Exception\NoSelectedLanguageException
     */
    public function testIfExceptionHasStaticMessageAndCode(): void
    {
        $exception = new NoSelectedLanguageException();
        self::assertNotEquals('', $exception->getMessage());
        self::assertNotEquals(0, $exception->getCode());
    }
}
