<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler;

use Lochmueller\LanguageDetection\Check\BotAgentCheck;
use Lochmueller\LanguageDetection\Detect\BrowserLanguageDetect;
use Lochmueller\LanguageDetection\Event\BuildResponseEvent;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Negotiation\DefaultNegotiation;
use Lochmueller\LanguageDetection\Response\DefaultResponse;
use Lochmueller\LanguageDetection\Service\Normalizer;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Lochmueller\LanguageDetection\Tests\Unit\AbstractTest;
use Symfony\Component\DependencyInjection\Container;
use TYPO3\CMS\Core\EventDispatcher\EventDispatcher;
use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;

/**
 * @internal
 * @coversNothing
 */
abstract class AbstractHandlerTest extends AbstractTest
{
    protected function getSmallEventListenerStack(): EventDispatcher
    {
        $container = new Container();
        $container->set(BotAgentCheck::class, new BotAgentCheck());
        $container->set(BrowserLanguageDetect::class, new BrowserLanguageDetect());
        $container->set(DefaultNegotiation::class, new DefaultNegotiation(new Normalizer()));
        $container->set(DefaultResponse::class, new DefaultResponse(new SiteConfigurationService()));
        $provider = new ListenerProvider($container);
        $provider->addListener(CheckLanguageDetectionEvent::class, BotAgentCheck::class);
        $provider->addListener(DetectUserLanguagesEvent::class, BrowserLanguageDetect::class);
        $provider->addListener(NegotiateSiteLanguageEvent::class, DefaultNegotiation::class);
        $provider->addListener(BuildResponseEvent::class, DefaultResponse::class);

        return new EventDispatcher($provider);
    }
}
