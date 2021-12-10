<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use Lochmueller\LanguageDetection\Check\BackendUserListener;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
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
    protected function setUp(): void
    {
        $this->resetSingletonInstances = true;
        parent::setUp();
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BackendUserListener
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testWithoutDisableInSite(): void
    {
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')
            ->willReturn(['disableRedirectWithBackendSession' => false])
        ;
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetection($site, $request);

        $backendUserListener = new BackendUserListener(new SiteConfigurationService());
        $backendUserListener($event);

        self::assertTrue($event->isLanguageDetectionEnable());
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BackendUserListener
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testWithoutConfigurationInSite(): void
    {
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')
            ->willReturn([])
        ;
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetection($site, $request);

        $backendUserListener = new BackendUserListener(new SiteConfigurationService());
        $backendUserListener($event);

        self::assertTrue($event->isLanguageDetectionEnable());
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\BackendUserListener
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
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

        $userAspect = new UserAspect();
        if ($isLoginState) {
            $user = new \stdClass();
            $user->user = ['uid' => 1];
            $propertyReflection = new \ReflectionProperty($userAspect, 'user');
            $propertyReflection->setAccessible(true);
            $propertyReflection->setValue($userAspect, $user);
        }

        $context = new Context();
        $context->setAspect('backend.user', $userAspect);

        GeneralUtility::setSingletonInstance(Context::class, $context);

        $backendUserListener = new BackendUserListener(new SiteConfigurationService());
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
