<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use Lochmueller\LanguageDetection\Check\EnableCheck;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Check\EnableCheck::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Service\SiteConfigurationService::class)]
class EnableCheckTest extends AbstractUnitTest
{
    /**
     * @param array<string, bool>|array<string, false>|mixed[] $configuration
     */
    #[DataProvider('data')]
    public function testConfiguration(array $configuration, bool $result): void
    {
        $site = self::createStub(Site::class);
        $site->method('getConfiguration')->willReturn($configuration);
        $request = self::createStub(ServerRequestInterface::class);
        $event = new CheckLanguageDetectionEvent($site, $request);

        $enableCheck = new EnableCheck($this->getSiteConfigurationService());
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
