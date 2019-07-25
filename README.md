Becklyn Translations Bundle
===========================

This bundle provides several helpers for working with translations.

Installation
------------

First install this package:

```bash
composer require becklyn/translations 
```

The import the routing:


```yaml
_import.becklyn_translations:
    resource: '@BecklynTranslationsBundle/Resources/config/routes.yaml'
    prefix: /_v/translations/
```


Configuration
-------------


```yaml
becklyn_translations:
    extract:
        messages:
            - test
            - abc.*
        backend:
            - *.error
    cache_version: 1
```

The `extract` key defines which messages from which domain need to be extracted and dumped for usage in JavaScript.
It is a nested array, where the key is the domain and the values are the message keys. You can use `*` as a placeholder.

The dumped JavaScript is cached. If somehow the cache is busted, you can manually invalidate the cache by bumping the 
`cache_version`.


JS Dumper + Loader
------------------

This bundle exposes an endpoint that can be used to load translations in JS.
Include it in your Twig using the twig function:

```twig
{{- javascript_translations_init() -}}
```

It will automatically use the locale of the current master request. To override this behaviour a custom locale can be passed:
`javascript_translations_init(locale)`.


### Using the Translations

The dumped translations will be added to a global `window.TranslatorInit.data` object. These are nested maps, the 
outer map has the domain as key, the inner value is a mapping from key -> translation. 
