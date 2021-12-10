<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Response;

use Lochmueller\LanguageDetection\Event\BuildResponse;
use Lochmueller\LanguageDetection\Response\DefaultResponse;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 * @coversNothing
 */
class DefaultResponseTest extends AbstractTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponse
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testConfiguration(): void
    {
        $siteLanguage = $this->createStub(SiteLanguage::class);
        $siteLanguage->method('getBase')->willReturn(new Uri('/en/'));

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn(new Uri('/?test=1'));

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn([
            'forwardRedirectParameters' => '',
            'redirectHttpStatusCode' => 307,
        ]);

        $event = new BuildResponse(
            $site,
            $request,
            $siteLanguage
        );

        $backendUserListener = new DefaultResponse(new SiteConfigurationService());
        $backendUserListener($event);

        self::assertNotNull($event->getResponse());
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponse
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testConfigurationWithWrongErrorCode(): void
    {
        $siteLanguage = $this->createStub(SiteLanguage::class);
        $siteLanguage->method('getBase')->willReturn(new Uri('/en/'));

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn(new Uri('/?test=1'));

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn([
            'forwardRedirectParameters' => '',
            'redirectHttpStatusCode' => 0,
        ]);

        $event = new BuildResponse(
            $site,
            $request,
            $siteLanguage
        );

        $backendUserListener = new DefaultResponse(new SiteConfigurationService());
        $backendUserListener($event);

        self::assertNotNull($event->getResponse());
        self::assertEquals(307, $event->getResponse()->getStatusCode());
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponse
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testConfigurationWithRedirectParams(): void
    {
        $siteLanguage = $this->createStub(SiteLanguage::class);
        $siteLanguage->method('getBase')->willReturn(new Uri('/en/'));

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn(new Uri('/?test=1'));

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn([
            'forwardRedirectParameters' => 'test',
            'redirectHttpStatusCode' => 307,
        ]);

        $event = new BuildResponse(
            $site,
            $request,
            $siteLanguage
        );

        $backendUserListener = new DefaultResponse(new SiteConfigurationService());
        $backendUserListener($event);

        self::assertNotNull($event->getResponse());
        self::assertEquals(307, $event->getResponse()->getStatusCode());
        self::assertEquals('/en/?test=1', $event->getResponse()->getHeaderLine('location'));
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\BuildResponse
     * @covers \Lochmueller\LanguageDetection\Response\DefaultResponse
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testConfigurationWithSameUrl(): void
    {
        $siteLanguage = $this->createStub(SiteLanguage::class);
        $siteLanguage->method('getBase')->willReturn(new Uri('/en/'));

        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getUri')->willReturn(new Uri('/en/'));

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn([
            'forwardRedirectParameters' => 'test',
            'redirectHttpStatusCode' => 307,
        ]);

        $event = new BuildResponse(
            $site,
            $request,
            $siteLanguage
        );

        $backendUserListener = new DefaultResponse(new SiteConfigurationService());
        $backendUserListener($event);

        self::assertNull($event->getResponse());
    }
}
