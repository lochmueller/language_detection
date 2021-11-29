<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Handler;

use Lochmueller\LanguageDetection\Event\BuildResponse;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguages;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage;
use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoResponseException;
use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Core handler for handling the language detection, with all 4 core events.
 */
class LanguageDetectionHandler extends AbstractHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $site = $this->getSiteFromRequest($request);

        $check = new CheckLanguageDetection($site, $request);
        $this->eventDispatcher->dispatch($check);

        if (!$check->isLanguageDetectionEnable()) {
            throw new DisableLanguageDetectionException();
        }

        $detect = new DetectUserLanguages($site, $request);
        $this->eventDispatcher->dispatch($detect);

        if (empty($detect->getUserLanguages())) {
            throw new NoUserLanguagesException();
        }

        $negotiate = new NegotiateSiteLanguage($site, $request, $detect->getUserLanguages());
        $this->eventDispatcher->dispatch($negotiate);

        if (null === $negotiate->getSelectedLanguage()) {
            throw new NoSelectedLanguageException();
        }

        $response = new BuildResponse($site, $request, $negotiate->getSelectedLanguage());
        $this->eventDispatcher->dispatch($response);

        if (null === $response->getResponse()) {
            throw new NoResponseException();
        }

        return $response->getResponse();
    }
}
