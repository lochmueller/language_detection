<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Middleware;

use LD\LanguageDetection\Event\HandleLanguageDetection;
use LD\LanguageDetection\Event\NegotiateSiteLanguage;
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

    public function __construct(UserLanguages $userLanguages, EventDispatcherInterface $eventDispatcher)
    {
        $this->userLanguages = $userLanguages;
        $this->eventDispatcher = $eventDispatcher;
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

        $userLanguages = $this->userLanguages->get($site, $request);

        $event = new NegotiateSiteLanguage($site, $request, $userLanguages);
        $this->eventDispatcher->dispatch($event);

        if (null === $event->getSelectedLanguage()) {
            return $handler->handle($request);
        }

        $targetUri = $this->buildRedirectUri($site, $request, $event->getSelectedLanguage());
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

        $params = GeneralUtility::trimExplode(',', (string)$site->getConfiguration()['forwardRedirectParameters'] ?? '', true);
        parse_str((string)$request->getUri()->getQuery(), $requestQuery);
        parse_str((string)$target->getQuery(), $targetQuery);

        foreach ($params as $param) {
            if (isset($requestQuery[$param])) {
                $targetQuery[$param] = $requestQuery[$param];
            }
        }

        return $target->withQuery(http_build_query($targetQuery));
    }
}
