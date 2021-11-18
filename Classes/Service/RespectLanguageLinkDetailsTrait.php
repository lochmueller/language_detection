<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use LD\LanguageDetection\Check\EnableListener;
use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Inject the needed services or create it by yourself.
 * E.g. $languageRequest could be created by ServerRequestFactory::fromGlobals();.
 */
trait RespectLanguageLinkDetailsTrait
{
    protected EventDispatcherInterface $languageEventDispatcher;

    protected SiteFinder $languageSiteFinder;

    protected ServerRequest $languageRequest;

    /**
     * @return mixed[]
     */
    public function addLanguageParameterByDetection(array $linkDetails): array
    {
        if (LinkService::TYPE_PAGE !== $linkDetails['type']) {
            return $linkDetails;
        }
        $site = $this->languageSiteFinder->getSiteByPageId((int)$linkDetails['pageuid'] ?? 0);

        $check = new CheckLanguageDetection($site, $this->languageRequest);
        $enableListener = new EnableListener(new SiteConfigurationService());
        $enableListener($check);

        if (!$check->isLanguageDetectionEnable()) {
            return $linkDetails;
        }

        $detect = new DetectUserLanguages($site, $this->languageRequest);
        $this->languageEventDispatcher->dispatch($detect);

        if (empty($detect->getUserLanguages())) {
            return $linkDetails;
        }

        $negotiate = new NegotiateSiteLanguage($site, $this->languageRequest, $detect->getUserLanguages());
        $this->languageEventDispatcher->dispatch($negotiate);

        if (null !== $negotiate->getSelectedLanguage() && $negotiate->getSelectedLanguage()->enabled()) {
            $linkDetails['parameters'] = 'L=' . $negotiate->getSelectedLanguage()->getLanguageId();
        }

        return $linkDetails;
    }
}
