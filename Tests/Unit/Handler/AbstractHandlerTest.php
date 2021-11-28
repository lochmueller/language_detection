<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Tests\Unit\Handler;

use LD\LanguageDetection\Check\BotListener;
use LD\LanguageDetection\Detect\BrowserLanguage;
use LD\LanguageDetection\Event\BuildResponse;
use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use LD\LanguageDetection\Negotiation\DefaultNegotiation;
use LD\LanguageDetection\Response\DefaultResponse;
use LD\LanguageDetection\Service\Normalizer;
use LD\LanguageDetection\Service\SiteConfigurationService;
use LD\LanguageDetection\Tests\Unit\AbstractTest;
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
        $container->set(BotListener::class, new BotListener());
        $container->set(BrowserLanguage::class, new BrowserLanguage());
        $container->set(DefaultNegotiation::class, new DefaultNegotiation(new Normalizer()));
        $container->set(DefaultResponse::class, new DefaultResponse(new SiteConfigurationService()));
        $provider = new ListenerProvider($container);
        $provider->addListener(CheckLanguageDetection::class, BotListener::class);
        $provider->addListener(DetectUserLanguages::class, BrowserLanguage::class);
        $provider->addListener(NegotiateSiteLanguage::class, DefaultNegotiation::class);
        $provider->addListener(BuildResponse::class, DefaultResponse::class);

        return new EventDispatcher($provider);
    }
}
