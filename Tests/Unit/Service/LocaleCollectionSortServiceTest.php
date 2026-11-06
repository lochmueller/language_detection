<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Service\LocaleCollectionSortService::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
class LocaleCollectionSortServiceTest extends AbstractUnitTest
{
    public function testDefaultSortingOfCollection(): void
    {
        $collection = LocaleCollection::fromArray(['de']);

        $sortService = new LocaleCollectionSortService();

        $result = $sortService->addLocaleByMode($collection, new LocaleValueObject('en'));

        self::assertSame(['de', 'en'], array_map(fn(LocaleValueObject $item): string => (string)$item, $result->toArray()));
    }

    public function testReplaceSortingOfCollection(): void
    {
        $collection = LocaleCollection::fromArray(['de']);

        $sortService = new LocaleCollectionSortService();

        $result = $sortService->addLocaleByMode($collection, new LocaleValueObject('en'), LocaleCollectionSortService::SORT_REPLACE);

        self::assertSame(['en'], array_map(fn(LocaleValueObject $item): string => (string)$item, $result->toArray()));
    }
}
