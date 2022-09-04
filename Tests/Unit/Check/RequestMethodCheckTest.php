<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use Lochmueller\LanguageDetection\Check\RequestMethodCheck;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 *
 * @coversNothing
 */
class RequestMethodCheckTest extends AbstractUnitTest
{
    /**
     * @covers \Lochmueller\LanguageDetection\Check\RequestMethodCheck
     * @covers \Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent
     *
     * @dataProvider data
     */
    public function testConfiguration(string $method, bool $result): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn($method);

        $event = new CheckLanguageDetectionEvent($this->createMock(Site::class), $request);

        $requestMethodCheck = new RequestMethodCheck();
        $requestMethodCheck($event);

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
