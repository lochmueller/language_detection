<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Response;

use Lochmueller\LanguageDetection\Domain\Model\Dto\SiteConfiguration;
use Lochmueller\LanguageDetection\Event\BuildResponse;
use Lochmueller\LanguageDetection\Service\SiteConfigurationService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class DefaultResponse
{
    protected SiteConfigurationService $siteConfigurationService;

    public function __construct(SiteConfigurationService $siteConfigurationService)
    {
        $this->siteConfigurationService = $siteConfigurationService;
    }

    public function __invoke(BuildResponse $event): void
    {
        $config = $this->siteConfigurationService->getConfiguration($event->getSite());
        $targetUri = $this->buildRedirectUri($config, $event->getRequest(), $event->getSelectedLanguage());
        if ($this->checkSameUri($event->getRequest(), $targetUri)) {
            $code = $config->getRedirectHttpStatusCode();
            if ($code <= 0) {
                $code = 307;
            }

            $event->setResponse(new RedirectResponse((string)$targetUri, $code));
        }
    }

    protected function buildRedirectUri(SiteConfiguration $config, ServerRequestInterface $request, SiteLanguage $language): Uri
    {
        /** @var Uri $target */
        $target = $language->getBase();

        $params = GeneralUtility::trimExplode(',', $config->getForwardRedirectParameters(), true);
        parse_str($request->getUri()->getQuery(), $requestQuery);
        parse_str($target->getQuery(), $targetQuery);

        foreach ($params as $param) {
            if (isset($requestQuery[$param])) {
                $targetQuery[$param] = $requestQuery[$param];
            }
        }

        return $target->withQuery(http_build_query($targetQuery));
    }

    protected function checkSameUri(ServerRequestInterface $request, Uri $targetUri): bool
    {
        if ((string)$request->getUri() === (string)$targetUri) {
            return false;
        }

        if ('' === (string)$targetUri->getHost()) {
            $absoluteTargetUri = $targetUri->withScheme($request->getUri()->getScheme())->withHost($request->getUri()->getHost());
            if ((string)$request->getUri() === (string)$absoluteTargetUri) {
                return false;
            }
        }

        return true;
    }
}
