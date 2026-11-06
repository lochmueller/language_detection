<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Check;

use Lochmueller\LanguageDetection\Check\RequestMethodCheck;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractUnitTest;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * @internal
 */
#[CoversClass(\Lochmueller\LanguageDetection\Check\RequestMethodCheck::class)]
#[CoversClass(\Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent::class)]
class RequestMethodCheckTest extends AbstractUnitTest
{
    #[DataProvider('data')]
    public function testConfiguration(string $method, bool $result): void
    {
        $request = self::createStub(ServerRequestInterface::class);
        $request->method('getMethod')->willReturn($method);

        $event = new CheckLanguageDetectionEvent(self::createStub(Site::class), $request);

        $requestMethodCheck = new RequestMethodCheck();
        $requestMethodCheck($event);

        self::assertEquals($result, $event->isLanguageDetectionEnable());
    }

    /**
     * @return array<string, mixed[]>
     */
    public static function data(): array
    {
        return [
            'Right request method' => ['GET', true],
            'Wrong request method' => ['POST', false],
            'Wrong request method 2' => ['PUT', false],
        ];
    }
}
