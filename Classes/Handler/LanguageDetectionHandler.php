<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Handler;

use Lochmueller\LanguageDetection\Event\BuildResponseEvent;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoResponseException;
use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Core handler for handling the language detection, with all 4 core events.
 * Called via regular main middleware and check all page settings.
 */
class LanguageDetectionHandler extends AbstractHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $site = $this->getSiteFromRequest($request);

        $check = new CheckLanguageDetectionEvent($site, $request);
        $this->eventDispatcher->dispatch($check);

        if (!$check->isLanguageDetectionEnable()) {
            throw new DisableLanguageDetectionException();
        }

        $detect = new DetectUserLanguagesEvent($site, $request);
        $this->eventDispatcher->dispatch($detect);

        if ($detect->getUserLanguages()->isEmpty()) {
            throw new NoUserLanguagesException();
        }

        $negotiate = new NegotiateSiteLanguageEvent($site, $request, $detect->getUserLanguages());
        $this->eventDispatcher->dispatch($negotiate);

        if ($negotiate->getSelectedLanguage() === null) {
            throw new NoSelectedLanguageException();
        }

        $response = new BuildResponseEvent($site, $request, $negotiate->getSelectedLanguage());
        $this->eventDispatcher->dispatch($response);

        if ($response->getResponse() === null) {
            throw new NoResponseException();
        }

        return $response->getResponse();
    }
}
