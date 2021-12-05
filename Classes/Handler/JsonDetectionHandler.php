<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Handler;

use Lochmueller\LanguageDetection\Event\DetectUserLanguages;
use Lochmueller\LanguageDetection\Event\NegotiateSiteLanguage;
use Lochmueller\LanguageDetection\Handler\Exception\DisableLanguageDetectionException;
use Lochmueller\LanguageDetection\Handler\Exception\NoUserLanguagesException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\JsonResponse;

/**
 * Language detection via "language.json". Do not check if Detection is enabled and
 * always output language information.
 */
class JsonDetectionHandler extends AbstractHandler implements RequestHandlerInterface
{
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if ('/language.json' !== $request->getUri()->getPath()) {
            throw new DisableLanguageDetectionException('Wrong URI for JSON detection', 2_346_782);
        }

        $site = $this->getSiteFromRequest($request);

        $detect = new DetectUserLanguages($site, $request);
        $this->eventDispatcher->dispatch($detect);

        if (empty($detect->getUserLanguages())) {
            throw new NoUserLanguagesException();
        }

        $negotiate = new NegotiateSiteLanguage($site, $request, $detect->getUserLanguages());
        $this->eventDispatcher->dispatch($negotiate);

        if (null === $negotiate->getSelectedLanguage()) {
            $negotiate->setSelectedLanguage($site->getDefaultLanguage());
        }

        return new JsonResponse($negotiate->getSelectedLanguage()->toArray());
    }
}
