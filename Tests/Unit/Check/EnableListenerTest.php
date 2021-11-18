<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Check;

use LD\LanguageDetection\Check\EnableListener;
use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Service\SiteConfigurationService;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 * @coversNothing
 */
class EnableListenerTest extends AbstractTest
{
    /**
     * @covers       \LD\LanguageDetection\Check\EnableListener
     * @covers       \LD\LanguageDetection\Domain\Model\Dto\SiteConfiguration
     * @covers       \LD\LanguageDetection\Event\CheckLanguageDetection
     * @covers       \LD\LanguageDetection\Service\SiteConfigurationService
     * @dataProvider data
     *
     * @param array<string, bool>|array<string, false>|mixed[] $configuration
     */
    public function testConfiguration(array $configuration, bool $result): void
    {
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn($configuration);
        $request = $this->createMock(ServerRequestInterface::class);
        $event = new CheckLanguageDetection($site, $request);

        $backendUserListener = new EnableListener(new SiteConfigurationService());
        $backendUserListener($event);

        self::assertEquals($result, $event->isLanguageDetectionEnable());
    }

    /**
     * @return array<string, mixed[]>
     */
    public function data(): array
    {
        return [
            'Explicit enabled' => [['enableLanguageDetection' => true], true],
            'Explicit disabled' => [['enableLanguageDetection' => false], false],
            'Without configuration' => [[], true],
        ];
    }
}
