<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use LD\LanguageDetection\Tests\Unit\AbstractTest;

class NormalizerTest extends AbstractTest
{
    /**
     * @dataProvider normalizeProvider
     */
    public function test_normalize_list($base, $result): void
    {
        $normalizer = new Normalizer();

        self::assertEquals($result, $normalizer->normalize($base));
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
        ];
    }
}
