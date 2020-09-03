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
    protected UserLanguages $userLanguages;

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
            // @todo configure "/" move to listener base url
            return $handler->handle($request);
        }

        // @todo check Backend login and disable (as configuration)

        //$userLanguages = $this->userLanguages->get($site);

        //DebuggerUtility::var_dump($addIp);
        //die();
        //
        //DebuggerUtility::var_dump($userLanguages);
        //die();

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
    protected function getMatchConfiguration(array $browserLanguages)
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
}
