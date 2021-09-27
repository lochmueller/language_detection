<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use LD\LanguageDetection\Check\EnableListener;
use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

trait RespectLanguageLinkDetailsTrait
{
    protected EventDispatcherInterface $languageEventDispatcher;

    protected SiteFinder $languageSiteFinder;

    /**
     * @return mixed[]
     */
    public function addLanguageParameterByDetection(array $linkDetails): array
    {
        if (LinkService::TYPE_PAGE !== $linkDetails['type']) {
            return $linkDetails;
        }

        $site = $this->languageSiteFinder->getSiteByPageId((int)$linkDetails['pageuid']);
        $request = ServerRequestFactory::fromGlobals();

        $check = new CheckLanguageDetection($site, $request);
        $enableListener = GeneralUtility::makeInstance(EnableListener::class);
        $enableListener($check);

        if (!$check->isLanguageDetectionEnable()) {
            return $linkDetails;
        }

        $detect = new DetectUserLanguages($site, $request);
        $this->languageEventDispatcher->dispatch($detect);

        if (empty($detect->getUserLanguages())) {
            return $linkDetails;
        }

        $negotiate = new NegotiateSiteLanguage($site, $request, $detect->getUserLanguages());
        $this->languageEventDispatcher->dispatch($negotiate);

        if (null !== $negotiate->getSelectedLanguage() && $negotiate->getSelectedLanguage()->enabled()) {
            $linkDetails['parameters'] = 'L=' . $negotiate->getSelectedLanguage()->getLanguageId();
        }

        return $linkDetails;
    }
}
