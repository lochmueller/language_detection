<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Service;

use Lochmueller\LanguageDetection\Service\TcaLanguageSelection;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * @internal
 * @coversNothing
 */
class TcaLanguageSelectionTest extends AbstractTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Service\TcaLanguageSelection
     */
    public function testLanguageSelectionNoConfiguration(): void
    {
        $tcaLanguageSelection = new TcaLanguageSelection($this->getSiteFinder());
        $configuration = [];

        $tcaLanguageSelection->get($configuration);

        self::assertSame([], $configuration);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Service\TcaLanguageSelection
     */
    public function testLanguageSelectionWithNoFoundedSite(): void
    {
        $siteFinder = $this->createStub(SiteFinder::class);
        $siteFinder->method('getSiteByIdentifier')->willThrowException(new SiteNotFoundException());

        $tcaLanguageSelection = new TcaLanguageSelection($siteFinder);
        $configuration = [
            'row' => [
                'identifier' => '1',
            ],
        ];

        $tcaLanguageSelection->get($configuration);

        self::assertSame([
            'row' => [
                'identifier' => '1',
            ],
        ], $configuration);
    }

    /**
     * @covers \Lochmueller\LanguageDetection\Service\TcaLanguageSelection
     */
    public function testLanguageSelectionWithConfiguration(): void
    {
        $tcaLanguageSelection = new TcaLanguageSelection($this->getSiteFinder());
        $configuration = [
            'row' => [
                'identifier' => '1',
            ],
        ];

        $tcaLanguageSelection->get($configuration);

        $assert = [
            'row' => [
                'identifier' => '1',
            ],
            'items' => [
                ['', ''],
                ['DE', 1],
                ['EN', 2],
            ],
        ];

        self::assertSame($assert, $configuration);
    }

    protected function getSiteFinder(): SiteFinder
    {
        $site = new Site('dummy', 1, [
            'base' => 'https://www.dummy.de/',
            'forwardRedirectParameters' => '',
            'languages' => [
                [
                    'languageId' => 1,
                    'base' => '/',
                    'locale' => 'de_DE',
                    'title' => 'DE',
                ],
                [
                    'languageId' => 2,
                    'base' => '/en/',
                    'locale' => 'en_GB',
                    'title' => 'EN',
                ],
            ],
        ]);

        $siteFinder = $this->createStub(SiteFinder::class);
        $siteFinder->method('getSiteByIdentifier')->willReturn($site);

        return $siteFinder;
    }
}
