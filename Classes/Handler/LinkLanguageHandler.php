<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Handler;

use Lochmueller\LanguageDetection\Check\EnableCheck;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetectionEvent;
use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoSelectedLanguageException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
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

        $check = new CheckLanguageDetectionEvent($site, $request);
        $enableCheck = new EnableCheck(new SiteConfigurationService());
        $enableCheck($check);

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

        if (!$negotiate->getSelectedLanguage() instanceof \TYPO3\CMS\Core\Site\Entity\SiteLanguage) {
            throw new NoSelectedLanguageException();
        }

        $response = new NullResponse();

        return $response->withAddedHeader(self::HEADER_NAME, (string)$negotiate->getSelectedLanguage()->getLanguageId());
    }
}
