<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 * @coversNothing
 */
class PathListenerTest extends AbstractTest
{
    /**
     * @covers \LD\LanguageDetection\Check\PathListener
     * @covers \LD\LanguageDetection\Event\CheckLanguageDetection
     * @dataProvider data
     *
     * @param array<string, bool>|array<string, false>|mixed[] $config
     */
    public function testPathConfiguration(array $config, string $uri, bool $result): void
    {
        $site = $this->createStub(Site::class);
        $site->method('getConfiguration')->willReturn($config);

        $request = new ServerRequest($uri, null, 'php://input', []);
        $event = new CheckLanguageDetection($site, $request);

        $botListener = new PathListener();
        $botListener($event);

        self::assertEquals($result, $event->isLanguageDetectionEnable());
    }

    /**
     * @return array<string, mixed[]>
     */
    public function data(): array
    {
        return [
            'allowAllPaths and deeplink' => [
                ['allowAllPaths' => true],
                'https://www.google.de/deep-link/',
                true,
            ],
            'allowAllPaths and homepage' => [
                ['allowAllPaths' => true],
                'https://www.google.de/',
                true,
            ],
            'no allowAllPaths and deeplink' => [
                ['allowAllPaths' => false],
                'https://www.google.de/deep-link-more/',
                false,
            ],
            'no allowAllPaths and homepage' => [
                ['allowAllPaths' => false],
                'https://www.home-page.de/',
                true,
            ],
            'without allowAllPaths and deeplink' => [
                [],
                'https://www.google.de/deep-link-next/',
                false,
            ],
            'without allowAllPaths and homepage' => [
                [],
                'https://www.tester.de/',
                true,
            ],
        ];
    }
}
