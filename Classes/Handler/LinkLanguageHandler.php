<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Handler;

use Lochmueller\LanguageDetection\Check\EnableListener;
use Lochmueller\LanguageDetection\Event\CheckLanguageDetection;
use Lochmueller\LanguageDetection\Event\DetectUserLanguages;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage;
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

        $check = new CheckLanguageDetection($site, $request);
        $enableListener = new EnableListener(new SiteConfigurationService());
        $enableListener($check);

        if (!$check->isLanguageDetectionEnable()) {
            throw new DisableLanguageDetectionException();
        }

        $detect = new DetectUserLanguages($site, $request);
        $this->eventDispatcher->dispatch($detect);

        if ($detect->getUserLanguages()->isEmpty()) {
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
