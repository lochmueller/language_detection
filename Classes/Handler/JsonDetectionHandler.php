<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Handler;

use Lochmueller\LanguageDetection\Event\DetectUserLanguagesEvent;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguageEvent;
use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;

/**
 * Language detection via "language.json". Do not check if Detection is enabled and
 * always output language information.
 */
class JsonDetectionHandler extends AbstractHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ($request->getUri()->getPath() !== '/language.json') {
            throw new DisableLanguageDetectionException('Wrong URI for JSON detection', 2_346_782);
        }

        $site = $this->getSiteFromRequest($request);

        $detect = new DetectUserLanguagesEvent($site, $request);
        $this->eventDispatcher->dispatch($detect);

        if ($detect->getUserLanguages()->isEmpty()) {
            throw new NoUserLanguagesException();
        }

        $negotiate = new NegotiateSiteLanguageEvent($site, $request, $detect->getUserLanguages());
        $this->eventDispatcher->dispatch($negotiate);

        $selectedLanguage = $negotiate->getSelectedLanguage();
        if ($selectedLanguage === null) {
            return new JsonResponse($site->getDefaultLanguage()->toArray());
        }

        return new JsonResponse($selectedLanguage->toArray());
    }
}
