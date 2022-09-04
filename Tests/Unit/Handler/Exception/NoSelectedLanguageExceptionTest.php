<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler\Exception;

use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;

/**
 * @internal
 *
 * @coversNothing
 */
class NoSelectedLanguageExceptionTest extends AbstractUnitTest
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
