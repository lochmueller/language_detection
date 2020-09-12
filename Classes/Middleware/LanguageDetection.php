<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Middleware;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Event\NegotiateSiteLanguage;
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
 */
class LanguageDetection implements MiddlewareInterface
{
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Site $site */
        $site = $request->getAttribute('site');

        $check = new CheckLanguageDetection($site, $request);
        $this->eventDispatcher->dispatch($check);

        if (!$check->isLanguageDetectionEnable()) {
            return $handler->handle($request);
        }

        $detect = new DetectUserLanguages($site, $request);
        $this->eventDispatcher->dispatch($detect);

        $negotiate = new NegotiateSiteLanguage($site, $request, $detect->getUserLanguages());
        $this->eventDispatcher->dispatch($negotiate);

        if (null === $negotiate->getSelectedLanguage()) {
            return $handler->handle($request);
        }

        // @todo move to "BuildResponse" event and add DefaultBuilder
        $targetUri = $this->buildRedirectUri($site, $request, $negotiate->getSelectedLanguage());
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
