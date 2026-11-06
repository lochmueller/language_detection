<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Service\Normalizer;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Service\Normalizer::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject::class)]
class NormalizerTest extends AbstractUnitTest
{
    #[DataProvider('normalizeProvider')]
    public function testNormalize(string $base, string $result): void
    {
        $normalizer = new Normalizer();

        self::assertEquals($result, $normalizer->normalize($base));
    }

    public function testNormalizeList(): void
    {
        $normalizer = new Normalizer();

        $base = array_map(fn($item): string => $item[0], $this->normalizeProvider());

        $result = array_map(fn($item): string => $item[1], $this->normalizeProvider());

        self::assertEquals(array_values($result), $normalizer->normalizeList(LocaleCollection::fromArray(array_values($base)))->toArray());
    }

    /**
     * @return array<string, array<int, string>>
     */
    public static function normalizeProvider(): array
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
