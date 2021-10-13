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
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
     * @dataProvider data
     */
    public function testWithDisableConfigurationInSiteAndActiveBackendUser(bool $isLoginState, bool $disableRedirectWithBackendSession, bool $isEnabled): void
    {
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')
            ->willReturn(['disableRedirectWithBackendSession' => $disableRedirectWithBackendSession])
        ;
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetection($site, $request);

        $userAspect = $this->createStub(UserAspect::class);
        $userAspect->method('isLoggedIn')
            ->willReturn($isLoginState)
        ;

        $context = new Context();
        $context->setAspect('backend.user', $userAspect);

        GeneralUtility::setSingletonInstance(Context::class, $context);

        $backendUserListener = new BackendUserListener();
        $backendUserListener($event);

        self::assertSame($isLoginState, $context->getAspect('backend.user')->get('isLoggedIn'));
        self::assertSame($isEnabled, $event->isLanguageDetectionEnable());
    }

    /**
     * @return array<string, bool[]>
     */
    public function data(): array
    {
        return [
            'Active be user and disableRedirectWithBackendSession' => [true, true, false],
            'Inactive be user and disableRedirectWithBackendSession' => [false, true, true],
            'Active be user and no disableRedirectWithBackendSession' => [true, false, true],
            'Inactive be user and no disableRedirectWithBackendSession' => [false, false, true],
        ];
    }
}
