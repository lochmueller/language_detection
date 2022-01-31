<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\LinkLanguageHandler;
use Lochmueller\LanguageDetection\Service\RespectLanguageLinkDetailsTrait;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * @internal
 * @coversNothing
 */
class RespectLanguageLinkDetailsTraitTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Service\RespectLanguageLinkDetailsTrait
     */
    public function testTraitExecutionWithWrongType(): void
    {
        $traitObject = $this->createTraitMock();

        self::assertEquals(['type' => 'wrong'], $traitObject->addLanguageParameterByDetection(['type' => 'wrong']));
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\EnableCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     * @covers \Lochmueller\LanguageDetection\Service\RespectLanguageLinkDetailsTrait
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testTraitExecutionWithDisabledLanguageDetection(): void
    {
        $siteFinder = $this->createStub(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')
            ->willReturn(new Site('dummy', 1, ['enableLanguageDetection' => false]))
        ;

        $handler = $this->createStub(LinkLanguageHandler::class);
        $handler->method('handle')->willThrowException(new DisableLanguageDetectionException());

        $traitObject = $this->createTraitMock($handler, $siteFinder);

        $configuration = [
            'type' => LinkService::TYPE_PAGE,
            'pageuid' => 5,
        ];

        self::assertEquals($configuration, $traitObject->addLanguageParameterByDetection($configuration));
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Check\EnableCheck
     * @covers \Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent
     * @covers \Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent
     * @covers \Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent
     * @covers \Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation
     * @covers \Lochmueller\LanguageDetection\Service\Normalizer
     * @covers \Lochmueller\LanguageDetection\Service\RespectLanguageLinkDetailsTrait
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     */
    public function testTraitExecutionWithNoUserLanguages(): void
    {
        $site = $this->createStub(Site::class);

        $siteFinder = $this->createStub(SiteFinder::class);
        $siteFinder->method('getSiteByPageId')
            ->willReturn($site)
        ;

        $response = new NullResponse();
        $response = $response->withAddedHeader(LinkLanguageHandler::HEADER_NAME, '5');

        $handler = $this->createStub(LinkLanguageHandler::class);
        $handler->method('handle')->willReturn($response);

        $traitObject = $this->createTraitMock($handler, $siteFinder);

        $configuration = [
            'type' => LinkService::TYPE_PAGE,
            'pageuid' => 5,
        ];

        self::assertEquals([
            'type' => LinkService::TYPE_PAGE,
            'pageuid' => 5,
            'parameters' => 'L=5',
        ], $traitObject->addLanguageParameterByDetection($configuration));
    }

    protected function createTraitMock(?LinkLanguageHandler $linkLanguageHandler = null, ?SiteFinder $languageSiteFinder = null): object
    {
        $traitObject = $this->getObjectForTrait(RespectLanguageLinkDetailsTrait::class);
        $reflectionClass = new \ReflectionClass($traitObject);

        if (null !== $linkLanguageHandler) {
            $propertyLanguageEventDispatcher = $reflectionClass->getProperty('linkLanguageHandler');
            $propertyLanguageEventDispatcher->setAccessible(true);
            $propertyLanguageEventDispatcher->setValue($traitObject, $linkLanguageHandler);
        }

        if (null !== $languageSiteFinder) {
            $propertyLanguageSiteFinder = $reflectionClass->getProperty('languageSiteFinder');
            $propertyLanguageSiteFinder->setAccessible(true);
            $propertyLanguageSiteFinder->setValue($traitObject, $languageSiteFinder);
        }

        return $traitObject;
    }
}
