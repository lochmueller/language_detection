<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Check;

use LD\LanguageDetection\Check\BackendUserListener;
use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 * @coversNothing
 */
class BackendUserListenerTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Check\BackendUserListener
     */
    public function testInvalidSiteObject(): void
    {
        $site = $this->createMock(SiteInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetection($site, $request);

        $backendUserListener = new BackendUserListener();
        $backendUserListener($event);

        self::assertFalse($event->isLanguageDetectionEnable());
    }

    /**
     * @covers \LD\LanguageDetection\Check\BackendUserListener
     */
    public function testWithoutDisableInSite(): void
    {
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')
            ->willReturn(['disableRedirectWithBackendSession' => false])
        ;
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetection($site, $request);

        $backendUserListener = new BackendUserListener();
        $backendUserListener($event);

        self::assertTrue($event->isLanguageDetectionEnable());
    }

    /**
     * @covers \LD\LanguageDetection\Check\BackendUserListener
     */
    public function testWithoutConfigurationInSite(): void
    {
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')
            ->willReturn([])
        ;
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetection($site, $request);

        $backendUserListener = new BackendUserListener();
        $backendUserListener($event);

        self::assertTrue($event->isLanguageDetectionEnable());
    }

    /**
     * @covers \LD\LanguageDetection\Check\BackendUserListener
     */
    public function testWithDisableConfigurationInSiteAndActiveBackendUser(): void
    {
        self::markTestSkipped();
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')
            ->willReturn(['disableRedirectWithBackendSession' => true])
        ;
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetection($site, $request);

        $userAspect = $this->createStub(UserAspect::class);
        $userAspect->method('isLoggedIn')
            ->willReturn(true)
        ;

        GeneralUtility::makeInstance(Context::class)->setAspect('backend.user', $userAspect);

        $backendUserListener = new BackendUserListener();
        $backendUserListener($event);

        self::assertTrue(GeneralUtility::makeInstance(Context::class)->getAspect('backend.user')->get('isLoggedIn'));
        self::assertFalse($event->isLanguageDetectionEnable());
    }

    /**
     * @covers \LD\LanguageDetection\Check\BackendUserListener
     */
    public function testWithDisableConfigurationInSiteAndNoBackendUser(): void
    {
        self::markTestSkipped();
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')
            ->willReturn(['disableRedirectWithBackendSession' => true])
        ;
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetection($site, $request);

        $userAspect = $this->createStub(UserAspect::class);
        $userAspect->method('isLoggedIn')
            ->willReturn(false)
        ;

        GeneralUtility::makeInstance(Context::class)->setAspect('backend.user', $userAspect);

        $backendUserListener = new BackendUserListener();
        $backendUserListener($event);

        self::assertFalse(GeneralUtility::makeInstance(Context::class)->getAspect('backend.user')->get('isLoggedIn'));
        self::assertTrue($event->isLanguageDetectionEnable());
    }
}
