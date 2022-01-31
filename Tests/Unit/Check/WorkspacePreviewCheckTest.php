<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use GuzzleHttp\Psr7\Query;
use Lochmueller\LanguageDetection\Check\WorkspacePreviewCheck;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Site\Entity\SiteInterface;

/**
 * @internal
 * @coversNothing
 */
class WorkspacePreviewCheckTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Check\WorkspacePreviewCheck
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent
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

        $event = new CheckLanguageDetectionEvent($site, $request);

        $workspacePreviewCheck = new WorkspacePreviewCheck();
        $workspacePreviewCheck($event);

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
