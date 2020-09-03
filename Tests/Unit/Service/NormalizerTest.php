<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use LD\LanguageDetection\Tests\Unit\AbstractTest;

class NormalizerTest extends AbstractTest
{
    public function test_normalize_list(): void
    {
        $normalizer = new Normalizer();

        self::assertEquals('de_DE', $normalizer->normalize('de-de'));
    }
}
