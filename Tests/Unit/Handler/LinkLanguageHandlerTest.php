<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Handler;

use LD\LanguageDetection\Handler\LinkLanguageHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Http\ServerRequest;

/**
 * @internal
 * @coversNothing
 */
class LinkLanguageHandlerTest extends AbstractHandlerTest
{
    /**
     * @covers \LD\LanguageDetection\Handler\AbstractHandler
     * @covers \LD\LanguageDetection\Handler\LinkLanguageHandler
     */
    public function testCallLinkHandlerWithoutSite(): void
    {
        $this->expectExceptionCode(1_637_813_123);

        $handler = new LinkLanguageHandler($this->createMock(EventDispatcherInterface::class));
        $handler->handle(new ServerRequest());
    }
}
