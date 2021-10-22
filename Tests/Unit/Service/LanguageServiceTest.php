<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Service;

use LD\LanguageDetection\Service\LanguageService;
use LD\LanguageDetection\Tests\Unit\AbstractTest;

/**
 * @internal
 * @coversNothing
 */
class LanguageServiceTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Service\LanguageService
     */
    public function testGetLanguageForCountry(): void
    {
        $languageService = new LanguageService();
        self::assertEquals('de', $languageService->getLanguageByCountry('DE'));
        self::assertEquals('en', $languageService->getLanguageByCountry('US'));
    }
}
