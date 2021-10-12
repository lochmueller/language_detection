<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Check;

use LD\LanguageDetection\Event\CheckLanguageDetection;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Context\ContextAwareInterface;
use TYPO3\CMS\Core\Context\ContextAwareTrait;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class BackendUserListener implements ContextAwareInterface
{
    use ContextAwareTrait;

    public function __invoke(CheckLanguageDetection $event): void
    {
        $config = $event->getSite()->getConfiguration();
        $disableForBackendUser = $config['disableRedirectWithBackendSession'] ?? false;
        if (!$disableForBackendUser) {
            return;
        }

        if (null === $this->context) {
            // Check integration
            $this->setContext(GeneralUtility::makeInstance(Context::class));
        }
        if ($this->getContext()->getAspect('backend.user')->get('isLoggedIn')) {
            $event->disableLanguageDetection();
        }
    }
}
