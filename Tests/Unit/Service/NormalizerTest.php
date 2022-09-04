<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Service\Normalizer;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;

/**
 * @internal
 *
 * @coversNothing
 */
class NormalizerTest extends AbstractUnitTest
{
    /**
     * @dataProvider normalizeProvider
     *
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     */
    public function testNormalize(string $base, string $result): void
    {
        $normalizer = new Normalizer();

        self::assertEquals($result, $normalizer->normalize($base));
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     */
    public function testNormalizeList(): void
    {
        $normalizer = new Normalizer();

        $base = array_map(fn ($item): string => $item[0], $this->normalizeProvider());

        $result = array_map(fn ($item): string => $item[1], $this->normalizeProvider());

        self::assertEquals(array_values($result), $normalizer->normalizeList(LocaleCollection::fromArray(array_values($base)))->toArray());
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function normalizeProvider(): array
    {
        return [
            'default' => [
                'de-de',
                'de_DE',
            ],
            'default with encoding' => [
                'de_DE.UTF-8',
                'de_DE',
            ],
            'lower and upper wrong in country' => [
                'en-gB',
                'en_GB',
            ],
            'lower and upper wrong in language' => [
                'EN-us',
                'en_US',
            ],
            'lower and upper wrong in language and language only' => [
                'EN',
                'en',
            ],
        ];
    }
}
