<?php

declare(strict_types=1);

namespace Lochmueller\LanguageDetection\Domain\Model\Dto;

class SiteConfiguration
{
    protected bool $enableLanguageDetection;

    protected bool $disableRedirectWithBackendSession;

    protected string $addIpLocationToBrowserLanguage;

    protected bool $allowAllPaths;

    protected int $redirectHttpStatusCode;

    protected string $forwardRedirectParameters;

    protected int $fallbackDetectionLanguage;

    protected string $maxMindDatabasePath;

    protected int $maxMindAccountId;

    protected string $maxMindLicenseKey;

    public function __construct(
        bool $enableLanguageDetection,
        bool $disableRedirectWithBackendSession,
        string $addIpLocationToBrowserLanguage,
        bool $allowAllPaths,
        int $redirectHttpStatusCode,
        string $forwardRedirectParameters,
        int $fallbackDetectionLanguage,
        string $maxMindDatabasePath,
        int $maxMindAccountId,
        string $maxMindLicenseKey
    ) {
        $this->enableLanguageDetection = $enableLanguageDetection;
        $this->disableRedirectWithBackendSession = $disableRedirectWithBackendSession;
        $this->addIpLocationToBrowserLanguage = $addIpLocationToBrowserLanguage;
        $this->allowAllPaths = $allowAllPaths;
        $this->redirectHttpStatusCode = $redirectHttpStatusCode;
        $this->forwardRedirectParameters = $forwardRedirectParameters;
        $this->fallbackDetectionLanguage = $fallbackDetectionLanguage;
        $this->maxMindDatabasePath = $maxMindDatabasePath;
        $this->maxMindAccountId = $maxMindAccountId;
        $this->maxMindLicenseKey = $maxMindLicenseKey;
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

    public function getMaxMindDatabasePath(): string
    {
        return $this->maxMindDatabasePath;
    }

    public function getMaxMindAccountId(): int
    {
        return $this->maxMindAccountId;
    }

    public function getMaxMindLicenseKey(): string
    {
        return $this->maxMindLicenseKey;
    }
}
