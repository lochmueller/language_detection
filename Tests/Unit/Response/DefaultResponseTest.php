<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Response;

use LD\LanguageDetection\Event\BuildResponse;
use LD\LanguageDetection\Response\DefaultResponse;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
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

        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn([
            'forwardRedirectParameters' => '',
            'redirectHttpStatusCode' => 307,
        ]);

        $event = new BuildResponse(
            $site,
            $this->createMock(ServerRequestInterface::class),
            $siteLanguage
        );

        $backendUserListener = new DefaultResponse();
        $backendUserListener($event);

        self::assertNotNull($event->getResponse());
    }
}
