<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use Lochmueller\LanguageDetection\Check\EnableCheck;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 *
 * @coversNothing
 */
class EnableCheckTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Check\EnableCheck
     * @covers \Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent
     * @covers \Lochmueller\LanguageDetection\Service\SiteConfigurationService
     *
     * @dataProvider data
     *
     * @param array<string, bool>|array<string, false>|mixed[] $configuration
     */
    public function testConfiguration(array $configuration, bool $result): void
    {
        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn($configuration);
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetectionEvent($site, $request);

        $enableCheck = new EnableCheck(new SiteConfigurationService());
        $enableCheck($event);

        self::assertEquals($result, $event->isLanguageDetectionEnable());
    }

    /**
     * @return array<string, mixed[]>
     */
    public static function data(): array
    {
        return [
            'Explicit enabled' => [['enableLanguageDetection' => true], true],
            'Explicit disabled' => [['enableLanguageDetection' => false], false],
            'Without configuration' => [[], true],
        ];
    }
}
