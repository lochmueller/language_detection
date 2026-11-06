<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Domain\Model\Dto;

use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
class LocaleValueObjectTest extends AbstractUnitTest
{
    public function testGetterOfDto(): void
    {
        $dto = new LocaleValueObject('test');

        self::assertEquals('test', $dto->getLocale());
        self::assertEquals('test', (string)$dto);

        $dto->setLocale('new');

        self::assertEquals('new', $dto->getLocale());
        self::assertEquals('new', (string)$dto);
    }
}
