<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler\Exception;

use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;

/**
 * @internal
 *
 * @coversNothing
 */
class DisableLanguageDetectionExceptionTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException
     */
    public function testIfExceptionHasStaticMessageAndCode(): void
    {
        $exception = new DisableLanguageDetectionException();
        self::assertNotEquals('', $exception->getMessage());
        self::assertNotEquals(0, $exception->getCode());
    }
}
