<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Tests\Fixtures;

use Lochmueller\LanguageDetection\Handler\LinkLanguageHandler;
use Lochmueller\LanguageDetection\Service\RespectLanguageLinkDetailsTrait;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Concrete class to test the trait in isolation.
 *
 * @internal
 */
final class RespectLanguageLinkDetailsTraitFixture
{
    use RespectLanguageLinkDetailsTrait;

    public function __construct(?SiteFinder $languageSiteFinder = null, ?LinkLanguageHandler $linkLanguageHandler = null)
    {
        if ($languageSiteFinder instanceof SiteFinder) {
            $this->languageSiteFinder = $languageSiteFinder;
        }

        if ($linkLanguageHandler instanceof LinkLanguageHandler) {
            $this->linkLanguageHandler = $linkLanguageHandler;
        }
    }
}
