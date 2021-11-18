<?php

declare(strict_types=1);

namespace LD\LanguageDetection\Domain\Model\Dto;

class SiteConfiguration
{
    protected bool $enableLanguageDetection;

    protected bool $disableRedirectWithBackendSession;

    protected string $addIpLocationToBrowserLanguage;

    protected bool $allowAllPaths;

    protected int $redirectHttpStatusCode;

    protected string $forwardRedirectParameters;

    protected int $fallbackDetectionLanguage;

    public function __construct(
        $enableLanguageDetection,
        $disableRedirectWithBackendSession,
        $addIpLocationToBrowserLanguage,
        $allowAllPaths,
        $redirectHttpStatusCode,
        $forwardRedirectParameters,
        $fallbackDetectionLanguage
    ) {
        $this->enableLanguageDetection = (bool)$enableLanguageDetection;
        $this->disableRedirectWithBackendSession = (bool)$disableRedirectWithBackendSession;
        $this->addIpLocationToBrowserLanguage = (string)$addIpLocationToBrowserLanguage;
        $this->allowAllPaths = (bool)$allowAllPaths;
        $this->redirectHttpStatusCode = (int)$redirectHttpStatusCode;
        $this->forwardRedirectParameters = (string)$forwardRedirectParameters;
        $this->fallbackDetectionLanguage = (int)$fallbackDetectionLanguage;
    }

    public function isEnableLanguageDetection(): bool
    {
        return $this->enableLanguageDetection;
    }

    public function isDisableRedirectWithBackendSession(): bool
    {
        return $this->disableRedirectWithBackendSession;
    }

    public function getAddIpLocationToBrowserLanguage(): string
    {
        return $this->addIpLocationToBrowserLanguage;
    }

    public function isAllowAllPaths(): bool
    {
        return $this->allowAllPaths;
    }

    public function getRedirectHttpStatusCode(): int
    {
        return $this->redirectHttpStatusCode;
    }

    public function getForwardRedirectParameters(): string
    {
        return $this->forwardRedirectParameters;
    }

    public function getFallbackDetectionLanguage(): int
    {
        return $this->fallbackDetectionLanguage;
    }
}
