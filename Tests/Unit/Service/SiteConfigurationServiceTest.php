<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Service;

use LD\LanguageDetection\Service\SiteConfigurationService;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class SiteConfigurationServiceTest extends AbstractTest
{
    /**
     * @covers       \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers       \LD\LanguageDetection\Service\SiteConfigurationService
     */
    public function testCreationOfConfigurationDto(): void
    {
        $service = new SiteConfigurationService();
        $config = $service->getConfiguration($this->createMock(SiteInterface::class));

        self::assertEquals(307, $config->getRedirectHttpStatusCode());
    }
}
