<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Service;

use Exception;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

class UserLanguages
{
    protected BrowserLanguage $browserLanguage;

    protected IpPosition $ipPosition;

    protected Normalizer $normalizer;

    public function __construct(BrowserLanguage $browserLanguage, IpPosition $ipPosition, Normalizer $normalizer)
    {
        $this->browserLanguage = $browserLanguage;
        $this->ipPosition = $ipPosition;
        $this->normalizer = $normalizer;
    }

    public function get(Site $site, ServerRequestInterface $request): array
    {
        $config = $site->getConfiguration();
        $addIp = $config['addIpLocationToBrowserLanguage'] ?? '';
        $browserLanguages = $this->browserLanguage->get($request);
        if ($addIp) {
            try {
                $language = $this->ipPosition->getLanguage($request);
                if (null === $language) {
                    throw new \Exception('No language found via IP', 12637);
                }
                switch ($addIp) {
                    case 'before':
                        array_unshift($browserLanguages, $language);
                        break;
                    case 'after':
                        $browserLanguages[] = $language;
                        break;
                    case 'replace':
                        $browserLanguages = [$language];
                        break;
                }
            } catch (Exception $exception) {
            }
        }

        return $this->normalizer->normalizeList($browserLanguages);
    }
}
