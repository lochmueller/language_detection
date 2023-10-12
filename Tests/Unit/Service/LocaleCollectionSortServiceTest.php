<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;

/**
 * @covers \Lochmueller\LanguageDetection\Service\LocaleCollectionSortService
 *
 * @internal
 */
class LocaleCollectionSortServiceTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     */
    public function testDefaultSortingOfCollection(): void
    {
        $collection = LocaleCollection::fromArray(['de']);

        $sortService = new LocaleCollectionSortService();

        $result = $sortService->addLocaleByMode($collection, new LocaleValueObject('en'));

        self::assertSame(['de', 'en'], array_map(fn(LocaleValueObject $item): string => (string)$item, $result->toArray()));
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     */
    public function testReplaceSortingOfCollection(): void
    {
        $collection = LocaleCollection::fromArray(['de']);

        $sortService = new LocaleCollectionSortService();

        $result = $sortService->addLocaleByMode($collection, new LocaleValueObject('en'), LocaleCollectionSortService::SORT_REPLACE);

        self::assertSame(['en'], array_map(fn(LocaleValueObject $item): string => (string)$item, $result->toArray()));
    }
}
