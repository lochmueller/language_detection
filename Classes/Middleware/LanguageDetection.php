<?php

namespace LD\LanguageDetection\Middleware;

use LD\LanguageDetection\Service\UserLanguages;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * LanguageDetection
 * @todo move old logic to services/events
 */
class LanguageDetection implements MiddlewareInterface
{
    /**
     * @var $userLanguages
     */
    protected $userLanguages;

    public function __construct(UserLanguages $userLanguages)
    {
        $this->userLanguages = $userLanguages;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var \TYPO3\CMS\Core\Site\Entity\Site $site */
        $site = $request->getAttribute('site');
        $config = $site->getConfiguration();

        $enable = $config['enableLanguageDetection'] ?? true;
        if (!$enable || $request->getUri()->getPath() !== '/') {
            // @todo configure "/"
            return $handler->handle($request);
        }

        // @todo check Backend login and disable (as configuration)

        $userLanguages = $this->userLanguages->get($site);

        //DebuggerUtility::var_dump($addIp);
        //die();
        //
        DebuggerUtility::var_dump($userLanguages);
        die();

        // HttpUtility::redirect($this->redirectUri, $httpStatus); // @todo configuration for status code

        return $handler->handle($request);
    }

    /**
     * Build up the redirect URI.
     *
     * @return string|bool
     */
    protected function getRedirectUri()
    {
        $this->messages[] = 'Get the redirect URI...';

        /** @var $matchConfiguration \HDNET\Hdnet\Domain\Model\DetectionConfiguration */
        $matchConfiguration = $this->getMatchConfiguration($this->browserLanguages);
        if (!\is_object($matchConfiguration)) {
            $this->messages[] = '... no matching object found for the redirect URI!!!';

            return false;
        }

        if ($matchConfiguration->getExt()) {
            $parts = \parse_url($matchConfiguration->getExt());
            if (!isset($parts['scheme'])) {
                $parts['scheme'] = 'http';
            }

            return HttpUtility::buildUrl($parts);
        }

        $targetPage = $matchConfiguration->getPage() > 0 ? $matchConfiguration->getPage() : $GLOBALS['TSFE']->id;
        $params = ['L' => $matchConfiguration->getLanguage()];

        if (null !== GeneralUtility::_GET('gclid')) {
            $params['gclid'] = GeneralUtility::_GET('gclid');
        }

        return $this->uriBuilder->reset()
            ->setCreateAbsoluteUri(true)
            ->setTargetPageUid($targetPage)
            ->setArguments($params)
            ->build();
    }

    /**
     * Get the redirect object for the language detection.
     *
     * @param array $browserLanguages
     *
     * @return LanguageDetection
     */
    protected function getMatchConfiguration($browserLanguages)
    {
        $configs = $this->currentConfiguration->getConfigurations()
            ->toArray();

        $this->messages[] = 'Get the match configurations...';
        $this->messages[] .= '... there are ' . \count($configs) . ' configurations.';
        if ('language_to_config' === $this->currentConfiguration->getProcessOrder()) {
            $this->messages[] = '... order is language_to_config';

            foreach ($browserLanguages as $browserLanguage) {
                foreach ($configs as $c) {
                    /** @var $c \HDNET\Hdnet\Domain\Model\DetectionConfiguration */
                    $match = \preg_match('#' . $c->getMatching() . '#', $browserLanguage);
                    $this->messages[] = '... match: ' . $browserLanguage . ' vs. ' . $c->getMatching() . ' = ' . ($match ? 'Y' : 'N');
                    if ($match) {
                        return $c;
                    }
                }
            }
        } else {
            $this->messages[] = '... order is config_to_language';

            foreach ($configs as $c) {
                foreach ($browserLanguages as $browserLanguage) {
                    /** @var $c \HDNET\Hdnet\Domain\Model\DetectionConfiguration */
                    $match = \preg_match('#' . $c->getMatching() . '#', $browserLanguage);
                    $this->messages[] = '... match: ' . $browserLanguage . ' vs. ' . $c->getMatching() . ' = ' . ($match ? 'Y' : 'N');
                    if ($match) {
                        return $c;
                    }
                }
            }
        }

        $this->messages[] = '... no match found!';
        $this->messages[] = 'Check the default configurations...';

        // default run
        if (\count($configs)) {
            switch ($this->currentConfiguration->getDefaultPosition()) {
                case 'first':
                    $this->messages[] = '... select first.';

                    return $configs[0];
                case 'last':
                    $this->messages[] = '... select last.';

                    return $configs[\count($configs) - 1];
                default:
                    $this->messages[] = '... no match with config size!!!';

                    return;
            }
        }

        $this->messages[] = '... no match and no config size!!!';
    }

    /**
     * Check the same redirect Uri.
     *
     * @return LanguageDetectionService
     * @throws LanguageDetectionException
     */
    protected function checkSameRedirectUri()
    {
        $this->messages[] = 'Check same URI...';
        if (!$this->inHomePageMode() && GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL') === $this->redirectUri) {
            $this->messages[] = '... same as current page!!!';
            throw new LanguageDetectionException('TYPO3_REQUEST_URL === redirect', 1470995534);
        }
        $this->messages[] = '... not the same as current page!!!';

        return $this;
    }

    /**
     * Check dry run.
     *
     * @param bool $dryRun
     *
     * @return LanguageDetectionService
     * @throws LanguageDetectionException
     */
    protected function checkDryRun($dryRun)
    {
        $this->messages[] = 'Check dry run...';
        if (!$this->inHomePageMode() && $dryRun) {
            $this->messages[] = '... dry run!!!';
            throw new LanguageDetectionException('dryRun: ' . $this->redirectUri, 1470995535);
        }
        $this->messages[] = '... no dry run';

        return $this;
    }

    /**
     * Check Invalid Redirected Uri.
     *
     * @return LanguageDetectionService
     * @throws LanguageDetectionException
     */
    protected function checkInvalidRedirectUri()
    {
        $this->messages[] = 'Check invalid redirect URI...';
        if (!$this->redirectUri or !\mb_strlen($this->redirectUri)) {
            $this->messages[] = '... it is no valid URI!!!';
            throw new LanguageDetectionException('No valid URI: ' . $this->redirectUri, 1470995536);
        }
        $this->messages[] = '... the URI is valid';

        return $this;
    }

    /**
     * Build from the LanguageDetection configuration the HTTP Status.
     *
     * @return LanguageDetectionService
     */
    protected function buildRedirectHttpStatus()
    {
        $this->messages[] = 'Get the Redirect HTTP Status...';
        $status = $this->currentConfiguration->getHttpStatus();
        if (!$status) {
            $this->redirectHttpStatus = 'HTTP_STATUS_303';
        } else {
            $this->redirectHttpStatus = $status;
        }

        $this->messages[] = '... set: ' . $this->redirectHttpStatus;

        return $this;
    }

    /**
     * Retrun true if the user come from the current page.
     *
     * @return LanguageDetectionService
     * @throws LanguageDetectionException
     */
    protected function checkFromThisPage()
    {
        $this->messages[] = 'Check from this page...';
        $referer = GeneralUtility::getIndpEnv('HTTP_REFERER');
        $baseUrl = $GLOBALS['TSFE']->baseUrl;
        if (!$this->inHomePageMode() && \mb_strlen($referer) && (false !== \mb_stripos(
            $referer,
            GeneralUtility::getIndpEnv('TYPO3_SITE_URL')
        ) || false !== \mb_stripos(
            $referer,
            $baseUrl
        ) || false !== \mb_stripos(
            $referer . '/',
            GeneralUtility::getIndpEnv('TYPO3_SITE_URL')
        ) || false !== \mb_stripos(
                    $referer . '/',
                    $baseUrl
                ))
        ) {
            $this->messages[] = '... user is already from this page!!!';
            throw new LanguageDetectionException('fromThisPage', 1470995537);
        }
        $this->messages[] = '... user is not from this page';

        return $this;
    }

    /**
     * Return true if there is a L var in the URI.
     *
     * @return LanguageDetectionService
     * @throws LanguageDetectionException
     */
    protected function checkLanguageAlreadyChosen()
    {
        $this->messages[] = 'Check language already chosen...';
        $hasLanguageParameter = null !== GeneralUtility::_GP('L');
        if (!$this->inHomePageMode() && $hasLanguageParameter) {
            $this->messages[] = '... language is already chosen (L=' . GeneralUtility::_GP('L') . ')!!!';
            throw new LanguageDetectionException('languageAlreadyChosen', 1470995538);
        }
        $this->messages[] = '... no language / default language is selected';

        return $this;
    }

    /**
     * Return true if the user is in the Workspace Preview.
     *
     * @return LanguageDetectionService
     * @throws LanguageDetectionException
     */
    protected function checkWorkspacePreview()
    {
        $this->messages[] = 'Check preview link...';
        if (null !== GeneralUtility::_GP('ADMCMD_prev') || null !== GeneralUtility::_GP('ADMCMD_previewWS')) {
            $this->messages[] = '... yes, it is a preview link!!!';
            throw new LanguageDetectionException('workspacePreview', 1470995540);
        }
        $this->messages[] = '... no preview link';

        return $this;
    }

    /**
     * Checks uri in session.
     *
     * @return LanguageDetectionService
     * @throws LanguageDetectionException
     */
    protected function checkUriInSession()
    {
        $this->messages[] = 'Check URI in session...';
        if (!$this->inHomePageMode() && $this->sessionService->has('uri')) {
            $this->messages[] = '... found in session!!!';
            throw new LanguageDetectionException('one redirect per session', 1470995541);
        }
        $this->messages[] = '... not found in session';

        return $this;
    }

    /**
     * Send a LanguageDetection Header.
     *
     * @param string $msg
     */
    protected function sendHeader($msg)
    {
        static $headerCount = 0;
        \header('X-Note: LD-' . $headerCount . '-' . $msg);
        ++$headerCount;
    }
}
