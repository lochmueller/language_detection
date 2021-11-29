<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use Lochmueller\LanguageDetection\Check\RequestMethodListener;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 * @coversNothing
 */
class RequestMethodeListenerTest extends AbstractTest
{
    /**
     * @covers       \Lochmueller\LanguageDetection\Check\RequestMethodListener
     * @covers       \Lochmueller\LanguageDetection\Event\CheckLanguageDetection
     * @dataProvider data
     */
    public function testConfiguration(string $method, bool $result): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn($method);

        $event = new CheckLanguageDetection($this->createMock(Site::class), $request);

        $backendUserListener = new RequestMethodListener();
        $backendUserListener($event);

        self::assertEquals($result, $event->isLanguageDetectionEnable());
    }

    /**
     * @return array<string, mixed[]>
     */
    public function data(): array
    {
        return [
            'Right request method' => ['GET', true],
            'Wrong request method' => ['POST', false],
            'Wrong request method 2' => ['PUT', false],
        ];
    }
}
