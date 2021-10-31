<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Service;

use LD\LanguageDetection\Service\RespectLanguageLinkDetailsTrait;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * @internal
 * @coversNothing
 */
class RespectLanguageLinkDetailsTraitTest extends AbstractTest
{
    use RespectLanguageLinkDetailsTrait;

    /**
     * @covers \LD\LanguageDetection\Service\RespectLanguageLinkDetailsTrait
     */
    public function testTraitExecutionWithWrongType(): void
    {
        //$eventDispatcher = $this->getMockClass(EventDispatcherInterface::class);
        //$siteFinder = $this->getMockClass(SiteFinder::class);

        $traitObject = $this->getObjectForTrait(RespectLanguageLinkDetailsTrait::class);

        //$reflectionClass = new \ReflectionClass($traitObject);
        //$reflectionClass->getProperty('eventDispatcher')->setValue($traitObject, $eventDispatcher);
        //$reflectionClass->getProperty('siteFinder')->setValue($traitObject, $siteFinder);

        self::assertEquals(['type' => 'wrong'], $traitObject->addLanguageParameterByDetection(['type' => 'wrong']));
    }
}
