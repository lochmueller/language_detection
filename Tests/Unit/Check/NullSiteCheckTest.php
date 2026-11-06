<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use Lochmueller\LanguageDetection\Check\NullSiteCheck;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\NullSite;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Check\NullSiteCheck::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\GlobalConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\GlobalConfigurationService::class)]
class NullSiteCheckTest extends AbstractUnitTest
{
    public function testRegularSiteIsNotTouched(): void
    {
        $event = new CheckLanguageDetectionEvent(self::createStub(Site::class), self::createStub(ServerRequestInterface::class));

        $check = new NullSiteCheck($this->getGlobalConfigurationService());
        $check($event);

        self::assertTrue($event->isLanguageDetectionEnable());
    }

    public function testNullSiteIsDisabledByDefault(): void
    {
        $event = new CheckLanguageDetectionEvent(new NullSite(), self::createStub(ServerRequestInterface::class));

        $check = new NullSiteCheck($this->getGlobalConfigurationService());
        $check($event);

        self::assertFalse($event->isLanguageDetectionEnable());
    }

    public function testNullSiteIsEnabledByTheGlobalConfiguration(): void
    {
        $event = new CheckLanguageDetectionEvent(new NullSite(), self::createStub(ServerRequestInterface::class));

        $check = new NullSiteCheck($this->getGlobalConfigurationService(['enableNullSiteDetection' => '1']));
        $check($event);

        self::assertTrue($event->isLanguageDetectionEnable());
    }
}
