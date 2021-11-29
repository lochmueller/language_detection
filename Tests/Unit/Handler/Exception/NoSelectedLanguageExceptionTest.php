<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler\Exception;

use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;

/**
 * @internal
 * @coversNothing
 */
class NoSelectedLanguageExceptionTest extends AbstractTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException
     */
    public function testIfExceptionHasStaticMessageAndCode(): void
    {
        $exception = new NoSelectedLanguageException();
        self::assertNotEquals('', $exception->getMessage());
        self::assertNotEquals(0, $exception->getCode());
    }
}
