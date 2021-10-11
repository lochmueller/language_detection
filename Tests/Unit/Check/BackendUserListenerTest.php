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

/**
 * @internal
 * @coversNothing
 */
class BackendUserListenerTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Check\BackendUserListener
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
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
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
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
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     */
    public function testWithDisableConfigurationInSiteAndActiveBackendUser(): void
    {
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

        $context = new Context([
            'backend.user' => $userAspect,
        ]);

        $backendUserListener = new BackendUserListener();
        $backendUserListener->setContext($context);
        $backendUserListener($event);

        self::assertTrue($context->getAspect('backend.user')->get('isLoggedIn'), (string)$userAspect->isLoggedIn());
        self::assertFalse($event->isLanguageDetectionEnable(), (string)$userAspect->isLoggedIn());
    }

    /**
     * @covers \LD\LanguageDetection\Check\BackendUserListener
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     */
    public function testWithDisableConfigurationInSiteAndNoBackendUser(): void
    {
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

        $context = new Context([
            'backend.user' => $userAspect,
        ]);

        $backendUserListener = new BackendUserListener();
        $backendUserListener->setContext($context);
        $backendUserListener($event);

        self::assertFalse($context->getAspect('backend.user')->get('isLoggedIn'));
        self::assertTrue($event->isLanguageDetectionEnable(), (string)$userAspect->isLoggedIn());
    }
}
