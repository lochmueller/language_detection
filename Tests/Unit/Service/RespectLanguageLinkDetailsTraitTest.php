<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\LinkLanguageHandler;
use Lochmueller\LanguageDetection\Tests\Fixtures\RespectLanguageLinkDetailsTraitFixture;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Service\RespectLanguageLinkDetailsTrait::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Check\EnableCheck::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\Normalizer::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteConfigurationService::class)]
class RespectLanguageLinkDetailsTraitTest extends AbstractUnitTest
{
    public function testTraitExecutionWithWrongType(): void
    {
        $traitObject = $this->createTraitMock();

        self::assertEquals(['type' => 'wrong'], $traitObject->addLanguageParameterByDetection(['type' => 'wrong']));
    }

    public function testTraitExecutionWithDisabledLanguageDetection(): void
    {
        $siteFinder = self::createStub(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')
            ->willReturn(new Site('dummy', 1, ['enableLanguageDetection' => false]))
        ;

        $handler = self::createStub(LinkLanguageHandler::class);
        $handler->method('handle')->willThrowException(new DisableLanguageDetectionException());

        $traitObject = $this->createTraitMock($handler, $siteFinder);

        $configuration = [
            'type' => LinkService::TYPE_PAGE,
            'pageuid' => 5,
        ];

        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('GET', '/', []);

        self::assertEquals($configuration, $traitObject->addLanguageParameterByDetection($configuration, $request));
    }

    public function testTraitExecutionWithNoUserLanguages(): void
    {
        $site = self::createStub(Site::class);

        $siteFinder = self::createStub(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')
            ->willReturn($site)
        ;

        $response = new NullResponse();
        $response = $response->withAddedHeader(LinkLanguageHandler::HEADER_NAME, '5');

        $handler = self::createStub(LinkLanguageHandler::class);
        $handler->method('handle')->willReturn($response);

        $traitObject = $this->createTraitMock($handler, $siteFinder);

        $configuration = [
            'type' => LinkService::TYPE_PAGE,
            'pageuid' => 5,
        ];

        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('GET', '/', []);

        self::assertEquals([
            'type' => LinkService::TYPE_PAGE,
            'pageuid' => 5,
            'parameters' => 'L=5',
        ], $traitObject->addLanguageParameterByDetection($configuration, $request));
    }

    protected function createTraitMock(?LinkLanguageHandler $linkLanguageHandler = null, ?SiteFinder $languageSiteFinder = null): RespectLanguageLinkDetailsTraitFixture
    {
        return new RespectLanguageLinkDetailsTraitFixture($languageSiteFinder, $linkLanguageHandler);
    }
}
