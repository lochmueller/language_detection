# TYPO3 Language Detection

**Meta:**
[![start with why](https://img.shields.io/badge/start%20with-why%3F-brightgreen.svg?style=flat)](https://www.ted.com/talks/simon_sinek_how_great_leaders_inspire_action)
[![Latest Stable Version](https://poser.pugx.org/lochmueller/language-detection/v/stable)](https://packagist.org/packages/lochmueller/language-detection)
[![Total Downloads](https://poser.pugx.org/lochmueller/language-detection/downloads)](https://packagist.org/packages/lochmueller/language-detection)
[![License](https://poser.pugx.org/lochmueller/language-detection/license)](https://packagist.org/packages/lochmueller/language-detection)
[![Crowdin](https://badges.crowdin.net/typo3-extension-languagedetect/localized.svg)](https://crowdin.com/project/typo3-extension-languagedetect)
[![Average time to resolve an issue](https://isitmaintained.com/badge/resolution/lochmueller/language_detection.svg)](https://isitmaintained.com/project/lochmueller/language_detection "Average time to resolve an issue")
[![Percentage of issues still open](https://isitmaintained.com/badge/open/lochmueller/language_detection.svg)](https://isitmaintained.com/project/lochmueller/language_detection "Percentage of issues still open")

**Compatibility:**
[![TYPO3](https://img.shields.io/badge/TYPO3-10-orange.svg)](https://get.typo3.org/version/10)
[![TYPO3](https://img.shields.io/badge/TYPO3-11-orange.svg)](https://get.typo3.org/version/11)
[![TYPO3](https://img.shields.io/badge/TYPO3-12-orange.svg)](https://get.typo3.org/version/12)

**Quality:**
[![Test](https://github.com/lochmueller/language_detection/actions/workflows/Test.yml/badge.svg)](https://github.com/lochmueller/language_detection/actions/workflows/Test.yml)
[![codecov](https://codecov.io/gh/lochmueller/language_detection/branch/main/graph/badge.svg?token=7VI1WFAX8Z)](https://codecov.io/gh/lochmueller/language_detection)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/lochmueller/language_detection/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/lochmueller/language_detection/?branch=main)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%208-brightgreen.svg?style=flat)](https://github.com/lochmueller/language_detection/actions)

**Support:**
[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/lochmueller/19.99)
[![contributions welcome](https://img.shields.io/badge/contributions-welcome-brightgreen.svg?style=flat)](https://github.com/lochmueller/language_detection/issues)
[![Plant Tree](https://img.shields.io/treeware/trees/lochmueller/language_detection)](https://plant.treeware.earth/lochmueller/language_detection)

***

# Table of Contents
1. [Why?](#why)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Event Structure](#event-structure)
   1. [CheckLanguageDetectionEvent](#checklanguagedetectionevent)
   2. [DetectUserLanguagesEvent](#detectuserlanguagesevent)
   3. [NegotiateSiteLanguageEvent](#negotiatesitelanguageevent)
   4. [BuildResponseEvent](#buildresponseevent)
5. [Troubleshooting](#troubleshooting)
6. [Dev](#dev)
7. [Contribution](#contribution)
8. [Licence](#licence)

## Why?

Language Detection should be easy & simple to integrate and powerfully in development! TYPO3 Core do not handle language detection via client information. EXT:languag_detection use a PSR-15/PSR-7 middleware/request ([TYPO3 Documentation](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/RequestHandling/Index.html)) to handle a language detection logic via PSR-14 events([TYPO3 Documentation](https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/Events/EventDispatcher/Index.html)). Very flexible! Give it a try and checkout the future of language detection!

## Installation

> composer require lochmueller/language-detection

## Configuration

Use the site configuration module to configure the language detection. Just enable it, and it will work :) There are several configuration options for the Site configuration that handle the control events. The following screenshot show the options of the detection configuration.

![Configuration](https://raw.githubusercontent.com/lochmueller/language_detection/main/Resources/Public/Configuration.png)

## Event Structure

There are four central PSR-14 events that control the language detection. The attached list explain the different events and the default listener. The events are ordered in the execution order.

[Diagram](https://sequencediagram.org/index.html#initialData=C4S2BsFMAICVII4FdIGdjQGbgPYHcAoIgYXBEgDtgBaAPgBUBNABQHkBmALmgHEBRetADkAeiEEmbdnT4A3ShgDKwAIYBjANbcAEiooATKNGIALSJoAyegOZIV1yABFIwc6BwU5Cgl6rRl6hrUdJIc3Mo4AA7Q+i5uIB4ANNAgmNBqZpopqFgq4KiQEiwcMvJ+AZo6eoYwzq5qwACqBQBOVhS29mi+wD5lSqqawQzFXP7AUTFxDQkUyanQ4DZ2DjkqLTCYOEgGKRTpZArQG6iRHgVFUqVHFVrQugZGAHKQ1jigKq6KYJDtnQ49Po3QZBEKjcITaKxeruOYpNIUHDQAC2nwykH0i2WXTWGyw2wMlxKtB640CVUeMAAQkgQOB9PBTudIIDSbdhqRyFRuPB9OQGtAJliOitIPM0iczhQCultvToAAjGAK2n0gichTDUJjYh5cCCszQCiQPDC-4wRqwACSBERrmgOHkLWg2p5ryQS2dGwMkBaIA6ROktA13P8lEx2ugzC6sqoCiAA)
![Request flow](https://github.com/lochmueller/language_detection/blob/main/Documentation/Images/Diagram.svg?raw=true)

### CheckLanguageDetectionEvent

Check if the language detection should execute by the extension. You can register listeners for this event and call "disableLanguageDetection" on the event object to disable the language detection.

Default-Listener:

| Name                  | Description                                                                                                                       |
|-----------------------|-----------------------------------------------------------------------------------------------------------------------------------|
| BackendUserCheck      | Check if a backend user call the language detection and disable the redirect (respect "disableRedirectWithBackendSession" config) |
| BotAgentCheck         | Check if a bot call the language detection and disable the redirect                                                               |
| EnableCheck           | Check if the Language Detection is enabled in the current Site                                                                    |
| FromCurrentPageCheck  | Check the referrer and disable the redirect if the user comes from the current site                                               |
| PathCheck             | Check if the user call "/" and disable the redirect for other paths (respect "allowAllPaths" configuration)                       |
| WorkspacePreviewCheck | Check if the page is a workspace preview and disable the redirect                                                                 |

### DetectUserLanguagesEvent

This event collect user information to get the user languages. You can register your own detections and manipulate the data via "getUserLanguages" and "setUserLanguages".

Default-Listener:

| Name                  | Description                                                                                                                                         |
|-----------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------|
| BrowserLanguageDetect | Get the users "accept-language" languages                                                                                                           |
| GeoPluginDetect       | Send the IP to geoplugin.net and add the language of the location to the checked languages (respect "addIpLocationToBrowserLanguage" configuration) |
| MaxMindDetect         | Use MaxMind database or webservice to get the country information                                                                                   |

_Please keep data privacy in mind in case of the "IpLanguage" Listener!_

### NegotiateSiteLanguageEvent

This event calculates the best matching page language for the user. If you build your own listener. Please use "setSelectedLanguage" on the event. If a language is already selected the default listener will be skipped.

Default-Listener:

| Name                | Description                                                                                               |
|---------------------|-----------------------------------------------------------------------------------------------------------|
| DefaultNegotiation  | Check the Locale and TwoLetterIso of the TYPO3 languages against the user languages of the previous event |
| FallbackNegotiation | Handle a fallback, if there are no matches by the default negotiation                                     |

### BuildResponseEvent

The last event build the middleware response. You can overwrite this step. You have to use "setResponse" to set the response.

Default-Listener:

| Name            | Description                                                               |
|-----------------|---------------------------------------------------------------------------|
| DefaultResponse | Build the response object and respect the "redirectHttpStatusCode" config |


## Troubleshooting

> There are missing or wrong languages in the detection process. Why?

Do you check in incognito mode? The browser will not send all languages in incognito mode. So "wrong results" are possible. Please check the request header to TYPO3 in detail. Otherwise, perhaps the DefaultNegotiation do not handle the "best fitting language" selection process for your needs?

> Why the redirect not work on subpages?

The middleware is early in the middleware stack. There is no concept of links and translations (or even page UID). Furthermore, it is recommended not redirect on subpages. A user that call a subpage first bookmark the page or search in a search engine. In both cases the user already get the right language. I suggest hreflang tags so search engines get the right language of the content https://developers.google.com/search/docs/advanced/crawling/localized-versions 


## Dev

Run all code standards

> docker run --rm -it --volume $(pwd):/app prooph/composer:8.0 -d /app code:all

Execute tests with PHP  8.0:

> docker run --rm -it --volume $(pwd):/app prooph/composer:8.0 -d /app test:unit

With coverage:

> docker run --rm -it --volume $(pwd):/app cicnavi/dap:80 /app/.Build/bin/phpunit -c /app/phpunit.xml --coverage-text --testdox --coverage-html=/app/var/phpunit

Run Mutation tests:

> docker run --rm -it --workdir=/app/ --volume $(pwd):/app cicnavi/dap:80 /app/.Build/bin/infection -c /app/phpunit.xml

## Contribution

Thanks all for the great contribution to the project!

![GitHub Contributors Image](https://contrib.rocks/image?repo=lochmueller/language_detection)

## Licence            

This package is [Treeware](https://treeware.earth). If you use it in production, then we ask that you [**buy the world a tree**](https://plant.treeware.earth/lochmueller/language_detection) to thank us for our work. By contributing to the Treeware forest you’ll be creating employment for local families and restoring wildlife habitats.
