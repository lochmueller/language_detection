<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Response;

use Lochmueller\LanguageDetection\Event\BuildResponseEvent;
use Lochmueller\LanguageDetection\Response\DefaultResponse;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\BuildResponseEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Response\DefaultResponse::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteConfigurationService::class)]
class DefaultResponseTest extends AbstractUnitTest
{
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

        $response = $event->getResponse();
        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals(307, $response->getStatusCode());
    }

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

        $response = $event->getResponse();
        self::assertInstanceOf(ResponseInterface::class, $response);
        self::assertEquals(307, $response->getStatusCode());
        self::assertEquals('/en/?test=1', $response->getHeaderLine('location'));
    }

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
