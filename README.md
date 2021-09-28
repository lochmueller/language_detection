# TYPO3 Language Detection

## Installation

> composer require lochmueller/language-detection

## Configuration

Use the site configuration module to configure the language detection. Just enable it, and it will works :)

![Configuration](https://raw.githubusercontent.com/lochmueller/language_detection/master/Resources/Public/Configuration.png)

## Structure

There are a few central PSR-14 events that control the language detection. The attached list explain the different events and the default listener. The events are ordered in the execution order.

### Event: CheckLanguageDetection

Check if the language detection should executed by the extension. You can register listeners for this event and call "disableLanguageDetection" on the event object to disable the language detection.

Default-Listener:

| Name                     | Description                                                                                                                       |
| ------------------------ | --------------------------------------------------------------------------------------------------------------------------------- |
| BackendUserListener      | Check if a backend user call the language detection and disable the redirect (respect "disableRedirectWithBackendSession" config) |
| BotListener              | Check if a bot call the language detection and disable the redirect                                                               |
| EnableListener           | Check if the Language Detection is enabled in the current Site                                                                    |
| FromCurrentPageListener  | Check the referrer and disable the redirect if the user comes from the current site                                               |
| PathListener             | Check if the user call "/" and disable the redirect for other paths (respect "allowAllPaths" configuration)                       |
| WorkspacePreviewListener | Check if the page is a workspace preview and disable the redirect                                                                 |

### Event: DetectUserLanguages

This event collect user information to get the user languages. You can register your own detections and manipulate the data via "getUserLanguages" and "setUserLanguages".

Default-Listener:

| Name            | Description                                                                                                                                         |
| --------------- | --------------------------------------------------------------------------------------------------------------------------------------------------- |
| BrowserLanguage | Get the users "accept-language" langauges                                                                                                           |
| IpLanguage      | Send the IP to geoplugin.net and add the language of the location to the checked languages (respect "addIpLocationToBrowserLanguage" configuration) |

_Please keep data privacy in mind in case of the "IpLanguage" Listener!_

### Event: NegotiateSiteLanguage

This event calculate the best matching page language for the user. If you build your own listener. Please use "setSelectedLanguage" on the event. If a language is already selected the default listener will be skipped.

Default-Listener:

| Name                | Description                                                                                               |
| ------------------- | --------------------------------------------------------------------------------------------------------- |
| DefaultNegotiation  | Check the Locale and TwoLetterIso of the TYPO3 languages against the user languages of the previous event |
| FallbackNegotiation | Handle a fallback, if there are no matches by the default negotiation                                     |

### Event: BuildResponse

The last event build the middleware response. You can overwrite this step. You have to use "setResponse" to set the response.

Default-Listener:

| Name            | Description                                                               |
| --------------- | ------------------------------------------------------------------------- |
| DefaultResponse | Build the repsonse object and respect the "redirectHttpStatusCode" config |


## Troubleshooting

> There are missing or wrong languages in the detection process. Why?

Do you check in incognito mode? The browser will not send all languages in incognito mode. So "wrong results" are possible. Please check the request header to TYPO3 in detail. Otherwise, perhaps the DefaultNegotiation do not handle the "best fitting language" selection process for your needs?

> Why the redirect not work on subpages?

The middleware is early in the middleware stack. There is no concept of links and translations (or page UID). Furthermore it is recommended not redirect on sub pages. A user that call a subpage first bookmark the page or search in a search egine. In both cases the use get already the right languages. I suggest hreflang tags so search engines get the right language of the content https://developers.google.com/search/docs/advanced/crawling/localized-versions 


## Dev

Execute tests: 

> docker run --rm -it --volume $(pwd):/app prooph/composer:7.4 test
