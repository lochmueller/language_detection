<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Detect;

use Lochmueller\LanguageDetection\Detect\MaxMindDetect;
use Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Service\LanguageService;
use Lochmueller\LanguageDetection\Service\LocaleCollectionSortService;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @covers \Lochmueller\LanguageDetection\Detect\MaxMindDetect
 *
 * @internal
 */
class MaxMindDetectTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Collection\LocaleCollection
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\LocaleValueObject
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
     * @covers \Lochmueller\LanguageDetection\Service\LanguageService
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testNoProviderConfiguration(): void
    {
        $serverRequest = new ServerRequest(null, null, 'php://input', []);

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn([]);

        $event = new DetectUserLanguagesEvent($site, $serverRequest);
        $event->setUserLanguages(LocaleCollection::fromArray(['default']));

        $maxMindDetect = new MaxMindDetect(new LanguageService(), new SiteConfigurationService(), new LocaleCollectionSortService());
        $maxMindDetect($event);

        self::assertCount(1, $event->getUserLanguages()->toArray());
    }
}
