<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;

/**
 * @internal
 *
 * @coversNothing
 */
class LanguageServiceTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Service\LanguageService
     */
    public function testGetLanguageForCountry(): void
    {
        self::markTestSkipped('Check ResourceBundle');

        $languageService = new LanguageService();
        self::assertEquals('de', $languageService->getLanguageByCountry('DE'));
        self::assertEquals('en', $languageService->getLanguageByCountry('US'));
    }
}
