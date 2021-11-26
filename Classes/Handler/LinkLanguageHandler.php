<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Handler;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use LD\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use LD\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use LD\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\NullResponse;

/**
 * Link handler for handling the language detection, with all 4 core events.
 * But the response it not for redirect, it is a NullResponse.
 * Please use the detected language uid in the header.
 */
class LinkLanguageHandler extends AbstractHandler implements RequestHandlerInterface
{
    public const HEADER_NAME = 'X-LANGUAGE-DETECTION-UID';

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $site = $this->getSiteFromRequest($request);

        // @todo check same as:
        //$check = new CheckLanguageDetection($site, $this->languageRequest);
        //$enableListener = new EnableListener(new SiteConfigurationService());
        //$enableListener($check);

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

        $response = new NullResponse();

        return $response->withAddedHeader(self::HEADER_NAME, (string)$negotiate->getSelectedLanguage()->getLanguageId());
    }
}
