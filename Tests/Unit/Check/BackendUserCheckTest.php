<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use Lochmueller\LanguageDetection\Check\BackendUserCheck;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\UserAspect;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Check\BackendUserCheck::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteConfigurationService::class)]
class BackendUserCheckTest extends AbstractUnitTest
{
    protected function setUp(): void
    {
        $this->resetSingletonInstances = true;
        parent::setUp();
    }

    public function testWithoutDisableInSite(): void
    {
        $site = self::createStub(Site::class);
        $site->method('getConfiguration')
            ->willReturn(['disableRedirectWithBackendSession' => false])
        ;
        $request = self::createStub(ServerRequestInterface::class);
        $event = new CheckLanguageDetectionEvent($site, $request);

        $backendUserCheck = new BackendUserCheck($this->getSiteConfigurationService());
        $backendUserCheck($event);

        self::assertTrue($event->isLanguageDetectionEnable());
    }

    public function testWithoutConfigurationInSite(): void
    {
        $site = self::createStub(Site::class);
        $site->method('getConfiguration')
            ->willReturn([])
        ;
        $request = self::createStub(ServerRequestInterface::class);
        $event = new CheckLanguageDetectionEvent($site, $request);

        $backendUserCheck = new BackendUserCheck($this->getSiteConfigurationService());
        $backendUserCheck($event);

        self::assertTrue($event->isLanguageDetectionEnable());
    }

    #[DataProvider('data')]
    public function testWithDisableConfigurationInSiteAndActiveBackendUser(bool $isLoginState, bool $disableRedirectWithBackendSession, bool $isEnabled): void
    {
        $site = self::createStub(Site::class);
        $site->method('getConfiguration')
            ->willReturn(['disableRedirectWithBackendSession' => $disableRedirectWithBackendSession])
        ;
        $request = self::createStub(ServerRequestInterface::class);
        $event = new CheckLanguageDetectionEvent($site, $request);

        $backendUser = null;
        if ($isLoginState) {
            $backendUser = new BackendUserAuthentication();
            $backendUser->user = ['uid' => 1];
        }

        $userAspect = new UserAspect($backendUser);

        $context = new Context();
        $context->setAspect('backend.user', $userAspect);

        GeneralUtility::setSingletonInstance(Context::class, $context);

        $backendUserCheck = new BackendUserCheck($this->getSiteConfigurationService());
        $backendUserCheck($event);

        self::assertSame($isLoginState, $context->getAspect('backend.user')->get('isLoggedIn'));
        self::assertSame($isEnabled, $event->isLanguageDetectionEnable());
    }

    /**
     * @return array<string, bool[]>
     */
    public static function data(): array
    {
        return [
            'Active be user and disableRedirectWithBackendSession' => [true, true, false],
            'Inactive be user and disableRedirectWithBackendSession' => [false, true, true],
            'Active be user and no disableRedirectWithBackendSession' => [true, false, true],
            'Inactive be user and no disableRedirectWithBackendSession' => [false, false, true],
        ];
    }
}
