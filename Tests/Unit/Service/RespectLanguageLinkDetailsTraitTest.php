<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Service;

use LD\LanguageDetection\Service\RespectLanguageLinkDetailsTrait;
use LD\LanguageDetection\Tests\Unit\AbstractTest;

/**
 * @internal
 * @coversNothing
 */
class RespectLanguageLinkDetailsTraitTest extends AbstractTest
{
    public function testTraitExecutionWithWrongType(): void
    {
        /** @var RespectLanguageLinkDetailsTrait $traitObject */
        $traitObject = $this->getMockForTrait(RespectLanguageLinkDetailsTrait::class);

        self::assertEquals(['type' => 'wrong'], $traitObject->addLanguageParameterByDetection(['type' => 'wrong']));
    }
}
