<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Detect;

use Lochmueller\LanguageDetection\Detect\MaxMindDetect;
use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguages;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @covers \Lochmueller\LanguageDetection\Detect\MaxMindDetectTest
 *
 * @internal
 */
class MaxMindDetectTest extends AbstractTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguages
     * @covers \Lochmueller\LanguageDetection\Service\LanguageService
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testNoProviderConfiguration(): void
    {
        $serverRequest = new ServerRequest(null, null, 'php://input', []);

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn([]);

        $event = new DetectUserLanguages($site, $serverRequest);
        $event->setUserLanguages(LocaleCollection::fromArray(['default']));

        $maxMindDetect = new MaxMindDetect(new LanguageService(), new SiteConfigurationService());
        $maxMindDetect($event);

        self::assertCount(1, $event->getUserLanguages()->toArray());
    }
}
