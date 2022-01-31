<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class SiteConfigurationServiceTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testCreationOfConfigurationDto(): void
    {
        $service = new SiteConfigurationService();
        $config = $service->getConfiguration($this->createMock(SiteInterface::class));

        self::assertEquals(307, $config->getRedirectHttpStatusCode());
    }
}
