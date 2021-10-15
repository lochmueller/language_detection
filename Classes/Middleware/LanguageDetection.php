<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Middleware;

use LD\LanguageDetection\Event\BuildResponse;
use LD\LanguageDetection\Event\CheckLanguageDetection;
use LD\LanguageDetection\Event\DetectUserLanguages;
use LD\LanguageDetection\Event\NegotiateSiteLanguage;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Site\Entity\Site;

/**
 * LanguageDetection.
 */
class LanguageDetection implements MiddlewareInterface
{
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $this->processLanguageDetection($request);
        } catch (\Exception $exception) {
            return $handler->handle($request);
        }
    }

    protected function processLanguageDetection(ServerRequestInterface $request): ResponseInterface
    {
        /** @var Site $site */
        $site = $request->getAttribute('site');

        $check = new CheckLanguageDetection($site, $request);
        $this->eventDispatcher->dispatch($check);

        if (!$check->isLanguageDetectionEnable()) {
            throw new \Exception('Language Detection is disabled', 1_236_781);
        }

        $detect = new DetectUserLanguages($site, $request);
        $this->eventDispatcher->dispatch($detect);

        if (empty($detect->getUserLanguages())) {
            throw new \Exception('No user languages', 3_284_924);
        }

        $negotiate = new NegotiateSiteLanguage($site, $request, $detect->getUserLanguages());
        $this->eventDispatcher->dispatch($negotiate);

        if (null === $negotiate->getSelectedLanguage()) {
            throw new \Exception('No selectable language', 2_374_892);
        }

        $response = new BuildResponse($site, $request, $negotiate->getSelectedLanguage());
        $this->eventDispatcher->dispatch($response);

        if (null === $response->getResponse()) {
            throw new \Exception('No response was created', 7_829_342);
        }

        return $response->getResponse();
    }
}
