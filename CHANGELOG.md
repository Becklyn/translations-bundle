1.2.0
=====

*   (bug) Try searching in the current `Request`'s locale first before looking in the main `Request`.
*   (improvement) Drop support for Symfony 4.x.
*   (improvement) Set minimum PHP version to 7.4.
*   (improvement) Add support fpr PHP 8.x.
*   (internal) Update `TranslationsCacheWarmer` to match newer `CacheWarmerInterface` interface definitions.
*   (internal) Always require newest version of `becklyn/php-cs`.


1.1.0
=====

*   (feature) Add `LinkedTranslationBuilder` and `LinkedTranslationBuilderFactory`.


1.0.5
=====

*   (improvement) Add two level cache to improve prod performance.
*   (improvement) Make `ConfigCache` more reliable and avoid issues due to symfony's inconsistent resource tracking.   


1.0.4
=====

*   (improvement) Use `ConfigCache` instead of `symfony/cache`.


1.0.3
=====

*   (bug) Properly compile translations before dumping.
*   (internal) Update bundle infrastructure.


1.0.2
=====

*   Allow Symfony 5.


1.0.1
=====

*   Fix JSON output if the JSON structure contains slashes.


1.0.0
=====

Initial Release `\o/`
