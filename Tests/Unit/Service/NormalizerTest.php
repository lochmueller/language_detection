<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Service;

use LD\LanguageDetection\Service\Normalizer;
use LD\LanguageDetection\Tests\Unit\AbstractTest;

class NormalizerTest extends AbstractTest
{
    /**
     * @dataProvider normalizeProvider
     *
     * @param mixed $base
     * @param mixed $result
     */
    public function testNormalize($base, $result): void
    {
        $normalizer = new Normalizer();

        self::assertEquals($result, $normalizer->normalize($base));
    }

    public function testNormalizeList(): void
    {
        $normalizer = new Normalizer();

        $base = array_map(function ($item) {
            return $item[0];
        }, $this->normalizeProvider());

        $result = array_map(function ($item) {
            return $item[1];
        }, $this->normalizeProvider());

        self::assertEquals($result, $normalizer->normalizeList($base));
    }

    public function normalizeProvider(): array
    {
        return [
            [
                'de-de',
                'de_DE',
            ],
            [
                'de_DE.UTF-8',
                'de_DE',
            ],
            [
                'en-gB',
                'en_GB',
            ],
            [
                'EN-us',
                'en_US',
            ],
            [
                'EN',
                'en',
            ],
        ];
    }
}
