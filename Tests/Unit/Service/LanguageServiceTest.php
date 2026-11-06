<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Service\LanguageService::class)]
class LanguageServiceTest extends AbstractUnitTest
{
    public function testGetLanguageForCountry(): void
    {
        self::skipWithoutLikelySubtags();

        $languageService = new LanguageService();
        self::assertEquals('de', $languageService->getLanguageByCountry('DE'));
        self::assertEquals('en', $languageService->getLanguageByCountry('US'));
    }
}
