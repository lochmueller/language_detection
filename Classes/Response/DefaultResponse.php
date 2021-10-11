<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Response;

use LD\LanguageDetection\Event\BuildResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DefaultResponse
{
    public function __invoke(BuildResponse $event): void
    {
        $targetUri = $this->buildRedirectUri($event->getSite(), $event->getRequest(), $event->getSelectedLanguage());
        if ((string)$event->getRequest()->getUri() !== (string)$targetUri) {
            $config = $event->getSite()->getConfiguration();
            $code = $config['redirectHttpStatusCode'] ?? 307;
            if ((int)$code <= 0) {
                $code = 307;
            }

            if ((string) $event->getRequest()->getUri() !== (string) $targetUri) {
                $event->setResponse(new RedirectResponse((string) $targetUri, $code));
            }
        }
    }

    protected function buildRedirectUri(Site $site, ServerRequestInterface $request, SiteLanguage $language): UriInterface
    {
        /** @var Uri $target */
        $target = $language->getBase();

        $params = GeneralUtility::trimExplode(',', (string)$site->getConfiguration()['forwardRedirectParameters'] ?? '', true);
        parse_str($request->getUri()->getQuery(), $requestQuery);
        parse_str($target->getQuery(), $targetQuery);

        foreach ($params as $param) {
            if (isset($requestQuery[$param])) {
                $targetQuery[$param] = $requestQuery[$param];
            }
        }

        return $target->withQuery(http_build_query($targetQuery));
    }
}
