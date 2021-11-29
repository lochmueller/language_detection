<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use GuzzleHttp\Psr7\Query;
use Lochmueller\LanguageDetection\Check\WorkspacePreviewListener;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class WorkspacePreviewListenerTest extends AbstractTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Check\WorkspacePreviewListener
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @dataProvider data
     *
     * @param mixed[]|array<string, string> $queryParams
     */
    public function testWorkspacePreviewListener(array $queryParams, bool $result): void
    {
        $site = $this->createMock(SiteInterface::class);
        $uri = 'https://www.google.de/test-page?' . Query::build($queryParams);
        $request = new ServerRequest($uri, null, 'php://input', []);
        $request = $request->withQueryParams($queryParams);

        $event = new CheckLanguageDetection($site, $request);

        $backendUserListener = new WorkspacePreviewListener();
        $backendUserListener($event);

        self::assertEquals($result, $event->isLanguageDetectionEnable());
    }

    /**
     * @return array<string, array<array<string, string>|bool>>
     */
    public function data(): array
    {
        return [
            'no param' => [
                [],
                true,
            ],
            'other param' => [
                ['otherParam' => 'value'],
                true,
            ],
            'ws param 1' => [
                ['ADMCMD_prev' => 'value'],
                false,
            ],
            'ws param 2' => [
                ['ADMCMD_previewWS' => 'value'],
                false,
            ],
        ];
    }
}
