<?php declare(strict_types=1);

namespace Becklyn\Translations\Extractor;

use Becklyn\Translations\Cache\CacheDigestGenerator;
use Becklyn\Translations\Catalogue\CachedCatalogue;
use Becklyn\Translations\Catalogue\KeyCatalogue;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationsExtractor
{
    private const CACHE_PREFIX = "becklyn_translations.catalogue";


    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var TranslatorInterface
     */
    private $translator;


    /**
     * @var CacheDigestGenerator
     */
    private $cacheDigestGenerator;


    /**
     * @var KeyCatalogue
     */
    private $catalogue;


    /**
     * @param CacheInterface       $cache
     * @param TranslatorInterface  $translator
     * @param CacheDigestGenerator $cacheDigestGenerator
     * @param KeyCatalogue         $catalogue
     */
    public function __construct (
        CacheInterface $cache,
        TranslatorInterface $translator,
        CacheDigestGenerator $cacheDigestGenerator,
        KeyCatalogue $catalogue
    )
    {
        $this->cache = $cache;
        $this->translator = $translator;
        $this->cacheDigestGenerator = $cacheDigestGenerator;
        $this->catalogue = $catalogue;
    }


    /**
     * Fetches the catalogue for the given language.
     *
     * @param string $locale
     * @param bool   $useCache
     *
     * @return CachedCatalogue
     */
    public function fetchCatalogue (string $locale, bool $useCache = true) : CachedCatalogue
    {
        $fetchCallback = function () use ($locale)
        {
            $catalogueJson = \json_encode(
                $this->extractCatalogue($locale),
                JsonResponse::DEFAULT_ENCODING_OPTIONS | \JSON_THROW_ON_ERROR
            );

            return new CachedCatalogue(
                $this->cacheDigestGenerator->calculateDigest($catalogueJson),
                $catalogueJson
            );
        };

        return $useCache
            ? $this->cache->get(self::CACHE_PREFIX . ".{$locale}", $fetchCallback)
            : $fetchCallback();
    }


    /**
     * Freshly extracts the catalogue.
     *
     * @param string $locale
     *
     * @return array
     */
    private function extractCatalogue (string $locale) : array
    {
        if (!$this->translator instanceof TranslatorBagInterface)
        {
            throw new UnexpectedTypeException($this->translator, TranslatorBagInterface::class);
        }

        $catalogue = $this->translator->getCatalogue($locale);
        $toFetch = $this->catalogue->getPatterns();
        $result = [];

        foreach ($catalogue->getDomains() as $domain)
        {
            $patternsToExtract = $toFetch[$domain] ?? [];

            if (empty($patternsToExtract))
            {
                // nothing to export from this domain -> skip
                continue;
            }

            foreach ($catalogue->all($domain) as $key => $message)
            {
                foreach ($patternsToExtract as $patternToExtract)
                {
                    if (\preg_match($patternToExtract, $key))
                    {
                        $result[$domain][$key] = $message;
                        break;
                    }
                }
            }
        }

        return $result;
    }
}
