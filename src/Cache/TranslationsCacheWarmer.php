<?php declare(strict_types=1);

namespace Becklyn\Translations\Cache;

use Becklyn\Translations\Catalogue\KeyCatalogue;
use Becklyn\Translations\Extractor\TranslationsExtractor;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;

class TranslationsCacheWarmer implements CacheWarmerInterface
{
    private KeyCatalogue $keyCatalogue;
    private TranslationsExtractor $extractor;


    public function __construct (KeyCatalogue $keyCatalogue, TranslationsExtractor $extractor)
    {
        $this->keyCatalogue = $keyCatalogue;
        $this->extractor = $extractor;
    }


    /**
     * @inheritDoc
     */
    public function isOptional () : bool
    {
        return false;
    }


    /**
     * @inheritDoc
     */
    public function warmUp (string $cacheDir) : array
    {
        $locales = ["de", "en"];

        foreach ($this->keyCatalogue->getNamespaces() as $namespace)
        {
            foreach ($locales as $locale)
            {
                $this->extractor->resetCache($namespace, $locale);
            }
        }

        return [];
    }
}
