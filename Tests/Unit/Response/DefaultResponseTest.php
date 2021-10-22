<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Response;

use LD\LanguageDetection\Event\BuildResponse;
use LD\LanguageDetection\Response\DefaultResponse;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
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
     * @covers       \LD\LanguageDetection\Event\BuildResponse
     * @covers       \LD\LanguageDetection\Response\DefaultResponse
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

        $backendUserListener = new DefaultResponse();
        $backendUserListener($event);

        self::assertNotNull($event->getResponse());
    }

    /**
     * @covers       \LD\LanguageDetection\Event\BuildResponse
     * @covers       \LD\LanguageDetection\Response\DefaultResponse
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

        $backendUserListener = new DefaultResponse();
        $backendUserListener($event);

        self::assertNotNull($event->getResponse());
        self::assertEquals(307, $event->getResponse()->getStatusCode());
    }

    /**
     * @covers       \LD\LanguageDetection\Event\BuildResponse
     * @covers       \LD\LanguageDetection\Response\DefaultResponse
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

        $backendUserListener = new DefaultResponse();
        $backendUserListener($event);

        self::assertNotNull($event->getResponse());
        self::assertEquals(307, $event->getResponse()->getStatusCode());
        self::assertEquals('/en/?test=1', $event->getResponse()->getHeaderLine('location'));
    }

    /**
     * @covers       \LD\LanguageDetection\Event\BuildResponse
     * @covers       \LD\LanguageDetection\Response\DefaultResponse
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

        $backendUserListener = new DefaultResponse();
        $backendUserListener($event);

        self::assertNull($event->getResponse());
    }
}
