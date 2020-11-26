Becklyn Translations Bundle
===========================

This bundle provides several helpers for working with translations.

Installation
------------

First install this package:

```bash
composer require becklyn/translations 
```

Then import the routing:


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
        frontend:
            messages:
                - test
                - abc.*
            backend:
                - *.error
        admin:
            messages:
                - admin.test.*

    cache_version: 1
```

The `extract` key defines which messages from which domain need to be extracted and dumped for usage in JavaScript.
It is a nested array, where the first level key is the namespace (more on that below). The second level key is the 
translation domain and the values are the message keys. You can use `*` as a placeholder for the message keys.

The dumped JavaScript is cached. If somehow the cache is busted, you can manually invalidate the cache by bumping the 
`cache_version`.


Namespaces
----------

The translations dumps are separated into namespaces, so that there can be for example one dump for frontend 
translations and one for the backend translations. The namespaces are just labels and are passed to the init function (see the next chapter).


JS Dumper + Loader
------------------

This bundle exposes an endpoint that can be used to load translations in JS.
Include it in your Twig using the twig function:

```twig
{{- javascript_translations_init(namespace) -}}
```

It will automatically use the locale of the current master request. To override this behaviour a custom locale can be passed:


```twig
{{- javascript_translations_init(namespace, locale) -}}
```


### Using the Translations

The dumped translations will be added to a global `window.TranslatorInit.data` object. These are nested maps, the 
outer map has the domain as key, the inner value is a mapping from key → translation. 


Linked translation builder
--------------------------

This bundle provides a `LinkedTranslationBuilder` that can be used to handle translations that contain links. 
It can simply be used by using the `LinkedTranslationBuilderFactory` to create a new instance.
Then a list of used links can be added to the `LinkedTranslationBuilder`. Finally, the `LinkedTranslationBuilder` is able to create all needed parameters for the translation:

```php
$translator->trans("your.translation-key", $linkedTranslationBuilderFactory->create()
    ->link("beforeLink", "afterLink", $linkableInterface)
    ->link("beforeLinkTwo", "afterLinkTwo", $linkableInterfaceTwo)
    ->generateTranslationParameters()
);
```

The `LinkedTranslationBuilder` is also capable of handling links that have custom classes and contain icons.
