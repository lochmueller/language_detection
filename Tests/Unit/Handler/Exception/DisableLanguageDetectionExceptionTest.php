<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler\Exception;

use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException::class)]
class DisableLanguageDetectionExceptionTest extends AbstractUnitTest
{
    public function testIfExceptionHasStaticMessageAndCode(): void
    {
        $exception = new DisableLanguageDetectionException();
        self::assertNotEquals('', $exception->getMessage());
        self::assertNotEquals(0, $exception->getCode());
    }
}
