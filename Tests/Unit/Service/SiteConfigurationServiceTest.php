<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteConfigurationService::class)]
class SiteConfigurationServiceTest extends AbstractUnitTest
{
    public function testCreationOfConfigurationDto(): void
    {
        $service = new SiteConfigurationService();
        $config = $service->getConfiguration(self::createStub(SiteInterface::class));

        self::assertEquals(307, $config->getRedirectHttpStatusCode());
    }
}
