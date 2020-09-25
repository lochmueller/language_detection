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
        /** @var Site $site */
        $site = $request->getAttribute('site');

        $check = new CheckLanguageDetection($site, $request);
        $this->eventDispatcher->dispatch($check);

        if (!$check->isLanguageDetectionEnable()) {
            return $handler->handle($request);
        }

        $detect = new DetectUserLanguages($site, $request);
        $this->eventDispatcher->dispatch($detect);

        if (empty($detect->getUserLanguages())) {
            return $handler->handle($request);
        }

        $negotiate = new NegotiateSiteLanguage($site, $request, $detect->getUserLanguages());
        $this->eventDispatcher->dispatch($negotiate);

        if (null === $negotiate->getSelectedLanguage()) {
            return $handler->handle($request);
        }

        $response = new BuildResponse($site, $request, $negotiate->getSelectedLanguage());
        $this->eventDispatcher->dispatch($response);

        if (null !== $response->getResponse()) {
            return $response->getResponse();
        }

        return $handler->handle($request);
    }
}
