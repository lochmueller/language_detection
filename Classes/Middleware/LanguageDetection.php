<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Middleware;

use LD\LanguageDetection\Event\HandleLanguageDetection;
use LD\LanguageDetection\Service\LanguageNegotiation;
use LD\LanguageDetection\Service\UserLanguages;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * LanguageDetection.
 *
 * @todo move old logic to services/events
 */
class LanguageDetection implements MiddlewareInterface
{
    protected UserLanguages $userLanguages;
    protected EventDispatcherInterface $eventDispatcher;
    protected LanguageNegotiation $languageNegotiation;

    public function __construct(UserLanguages $userLanguages, EventDispatcherInterface $eventDispatcher, LanguageNegotiation $languageNegotiation)
    {
        $this->userLanguages = $userLanguages;
        $this->eventDispatcher = $eventDispatcher;
        $this->languageNegotiation = $languageNegotiation;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Site $site */
        $site = $request->getAttribute('site');

        $event = new HandleLanguageDetection($site, $request);
        $this->eventDispatcher->dispatch($event);

        if (!$event->isHandleLanguageDetection()) {
            return $handler->handle($request);
        }

        $userLanguages = $this->userLanguages->get($site);
        $siteLanguages = $site->getAllLanguages();

        $targetLanguage = $this->languageNegotiation->findBestSiteLanguage($userLanguages, $siteLanguages);
        if (null === $targetLanguage) {
            return $handler->handle($request);
        }

        $targetUri = $this->buildRedirectUri($site, $request, $targetLanguage);
        if ((string)$request->getUri() !== (string)$targetUri) {
            $config = $site->getConfiguration();
            $code = $config['redirectHttpStatusCode'] ?? 307;
            if ((int)$code <= 0) {
                $code = 307;
            }

            return new RedirectResponse((string)$targetUri, $code);
        }

        return $handler->handle($request);
    }

    protected function buildRedirectUri(Site $site, ServerRequestInterface $request, SiteLanguage $language): UriInterface
    {
        /** @var Uri $target */
        $target = $language->getBase();

        $config = $site->getConfiguration();
        $params = $config['forwardRedirectParamters'] ?? '';
        $params = GeneralUtility::trimExplode(',', $params, true);

        foreach ($params as $param) {
            $result = GeneralUtility::_GET($param);
            if (null !== $result) {
                $target = $target->withQuery(trim($target->getQuery() . '&' . $param . '=' . $result, '&'));
            }
        }

        return $target;
    }
}
