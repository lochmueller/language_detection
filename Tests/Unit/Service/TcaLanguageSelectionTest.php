<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Service;

use LD\LanguageDetection\Service\TcaLanguageSelection;
use LD\LanguageDetection\Tests\Unit\AbstractTest;

/**
 * @internal
 * @coversNothing
 */
class TcaLanguageSelectionTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Service\TcaLanguageSelection
     */
    public function testLanguageSelection(): void
    {
        $tcaLanguageSelection = new TcaLanguageSelection();
        $configuration = [];

        $tcaLanguageSelection->get($configuration);

        self::assertSame([], $configuration);
    }
}
