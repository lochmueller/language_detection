# TYPO3 Language Detection

## Installation

> composer require lochmueller/language-detection

## Configuration

Use the site configuration module to configure the language detection. Just enable it, and it will works :)

![Configuration](https://raw.githubusercontent.com/lochmueller/language_detection/master/Resources/Public/Configuration.jpg)

## Structure

There are a few central PSR-14 events that control the language detection. The attached list explain the different events and the default listener. The events are ordered in the execution order.

### Event: CheckLanguageDetection

Check if the language detection should executed by the extension. Default listeners are check of backend users, enable flag, Referrer check, path check and workspace preview check.

You can register listeners for this event and call "disableLanguageDetection" on the event object to disable the language detection.

### Event: DetectUserLanguages

This event collect user information to get the user languages. There are two default listener. One detect the language by browser language and one more complex that detect the language by the user IP (please keep data privacy in mind!).

You  can register your own detections and manipulate the data via "getUserLanguages" and "setUserLanguages".

### Event: NegotiateSiteLanguage

This event get the best matching page language for the user. There is one default listener that check Locale and TwoLetterIso of the TYPO3 languages against the user languages.

If you build your own listener. Please use "setSelectedLanguage" on the event. If a language is already selected the default listener will be skipped.

### Event: BuildResponse

The last event build the middleware response. There is one default listener that respect the "redirectHttpStatusCode" setting and create the response object.

You can overwrite this step. You have to use "setResponse" to set the response.
