<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Service;

use Lochmueller\LanguageDetection\Handler\Exception\AbstractHandlerException;
use Lochmueller\LanguageDetection\Handler\LinkLanguageHandler;
use TYPO3\CMS\Core\Http\ServerRequestFactory;
use TYPO3\CMS\Core\LinkHandling\LinkService;
use TYPO3\CMS\Core\Site\SiteFinder;

/**
 * Inject the needed services or create it by yourself.
 */
trait RespectLanguageLinkDetailsTrait
{
    protected SiteFinder $languageSiteFinder;

    protected LinkLanguageHandler $linkLanguageHandler;

    /**
     * @return mixed[]
     */
    public function addLanguageParameterByDetection(array $linkDetails): array
    {
        if (LinkService::TYPE_PAGE !== $linkDetails['type']) {
            return $linkDetails;
        }

        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest('GET', '/', []);
        $request = $request->withAttribute('site', $this->languageSiteFinder->getSiteByPageId((int)$linkDetails['pageuid'] ?? 0));

        try {
            $response = $this->linkLanguageHandler->handle($request);

            $linkDetails['parameters'] = 'L=' . $response->getHeaderLine(LinkLanguageHandler::HEADER_NAME);

            return $linkDetails;
        } catch (AbstractHandlerException $exception) {
            return $linkDetails;
        }
    }
}
