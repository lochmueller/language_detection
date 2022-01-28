<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Unit\Handler;

use Lochmueller\LanguageDetection\Check\BotAgentCheck;
use Lochmueller\LanguageDetection\Detect\BrowserLanguage;
use Lochmueller\LanguageDetection\Event\BuildResponse;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguages;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage;
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
        $container->set(BrowserLanguage::class, new BrowserLanguage());
        $container->set(DefaultNegotiation::class, new DefaultNegotiation(new Normalizer()));
        $container->set(DefaultResponse::class, new DefaultResponse(new SiteConfigurationService()));
        $provider = new ListenerProvider($container);
        $provider->addListener(CheckLanguageDetection::class, BotAgentCheck::class);
        $provider->addListener(DetectUserLanguages::class, BrowserLanguage::class);
        $provider->addListener(NegotiateSiteLanguage::class, DefaultNegotiation::class);
        $provider->addListener(BuildResponse::class, DefaultResponse::class);

        return new EventDispatcher($provider);
    }
}
