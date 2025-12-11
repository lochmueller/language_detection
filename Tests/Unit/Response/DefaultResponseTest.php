<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Response;

use Lochmueller\LanguageDetection\Event\BuildResponseEvent;
use Lochmueller\LanguageDetection\Response\DefaultResponse;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 *
 * @coversNothing
 */
class DefaultResponseTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponseEvent
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testConfiguration(): void
    {
        $siteLanguage = self::createStub(SiteLanguage::class);
        $siteLanguage->method('getBase')->willReturn(new Uri('/en/'));

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn(new Uri('/?test=1'));

        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn([
            'forwardRedirectParameters' => '',
            'redirectHttpStatusCode' => 307,
        ]);

        $event = new BuildResponseEvent(
            $site,
            $request,
            $siteLanguage
        );

        $defaultResponse = new DefaultResponse(new SiteConfigurationService());
        $defaultResponse($event);

        self::assertNotNull($event->getResponse());
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponseEvent
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testConfigurationWithWrongErrorCode(): void
    {
        $siteLanguage = self::createStub(SiteLanguage::class);
        $siteLanguage->method('getBase')->willReturn(new Uri('/en/'));

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn(new Uri('/?test=1'));

        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn([
            'forwardRedirectParameters' => '',
            'redirectHttpStatusCode' => 0,
        ]);

        $event = new BuildResponseEvent(
            $site,
            $request,
            $siteLanguage
        );

        $defaultResponse = new DefaultResponse(new SiteConfigurationService());
        $defaultResponse($event);

        self::assertNotNull($event->getResponse());
        if ($event->getResponse() instanceof \Psr\Http\Message\ResponseInterface) {
            self::assertEquals(307, $event->getResponse()->getStatusCode());
        }
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponseEvent
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testConfigurationWithRedirectParams(): void
    {
        $siteLanguage = self::createStub(SiteLanguage::class);
        $siteLanguage->method('getBase')->willReturn(new Uri('/en/'));

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn(new Uri('/?test=1'));

        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn([
            'forwardRedirectParameters' => 'test',
            'redirectHttpStatusCode' => 307,
        ]);

        $event = new BuildResponseEvent(
            $site,
            $request,
            $siteLanguage
        );

        $defaultResponse = new DefaultResponse(new SiteConfigurationService());
        $defaultResponse($event);

        self::assertNotNull($event->getResponse());
        if ($event->getResponse() instanceof \Psr\Http\Message\ResponseInterface) {
            self::assertEquals(307, $event->getResponse()->getStatusCode());
            self::assertEquals('/en/?test=1', $event->getResponse()->getHeaderLine('location'));
        }
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponseEvent
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testConfigurationWithSameUrl(): void
    {
        $siteLanguage = self::createStub(SiteLanguage::class);
        $siteLanguage->method('getBase')->willReturn(new Uri('/en/'));

        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn(new Uri('/en/'));

        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn([
            'forwardRedirectParameters' => 'test',
            'redirectHttpStatusCode' => 307,
        ]);

        $event = new BuildResponseEvent(
            $site,
            $request,
            $siteLanguage
        );

        $defaultResponse = new DefaultResponse(new SiteConfigurationService());
        $defaultResponse($event);

        self::assertNull($event->getResponse());
    }
}
