<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Middleware;

use LD\LanguageDetection\Event\HandleLanguageDetection;
use LD\LanguageDetection\Service\UserLanguages;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * LanguageDetection.
 *
 * @todo move old logic to services/events
 */
class LanguageDetection implements MiddlewareInterface
{
    protected UserLanguages $userLanguages;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(UserLanguages $userLanguages, EventDispatcherInterface $eventDispatcher)
    {
        $this->userLanguages = $userLanguages;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var Site $site */
        $site = $request->getAttribute('site');

        $event = new HandleLanguageDetection($site, $request);
        $this->eventDispatcher->dispatch($event);

        if (!$event->isHandleLanguageDetection()) {
            return $handler->handle($request);
        }

        //$userLanguages = $this->userLanguages->get($site);
        //$siteLanguages = $site->getAllLanguages();
        // Filter by enabled

        //DebuggerUtility::var_dump($userLanguages);
        //DebuggerUtility::var_dump($siteLanguages);
        //DebuggerUtility::var_dump((string)$this->buildRedirectUri($site, $siteLanguages[0]));
        //die();

        // redirectHttpStatusCode

        // HttpUtility::redirect($this->redirectUri, $httpStatus); // @todo configuration for status code

        return $handler->handle($request);
    }

    protected function buildRedirectUri(Site $site, SiteLanguage $language): UriInterface
    {
        /** @var Uri $target */
        $target = $language->getBase();

        // $target->getQuery()
        // $target->withQuery()

        // DebuggerUtility::var_dump($target);

        //if (null !== GeneralUtility::_GET('gclid')) {
        //    $params['gclid'] = GeneralUtility::_GET('gclid');
        //}

        return $target;
    }

    /**
     * Get the redirect object for the language detection.
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
                    $match = preg_match('#' . $c->getMatching() . '#', $browserLanguage);
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
                    $match = preg_match('#' . $c->getMatching() . '#', $browserLanguage);
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
     *
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
