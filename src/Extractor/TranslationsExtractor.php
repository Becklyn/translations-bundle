<?php declare(strict_types=1);

namespace Becklyn\Translations\Extractor;

use Becklyn\Translations\Cache\CacheDigestGenerator;
use Becklyn\Translations\Catalogue\CachedCatalogue;
use Becklyn\Translations\Catalogue\KeyCatalogue;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationsExtractor
{
    private const CACHE_PREFIX = "becklyn_translations.catalogue.%s.%s";

    /** @var CacheInterface */
    private $cache;

    /** @var TranslatorInterface */
    private $translator;

    /** @var CacheDigestGenerator */
    private $cacheDigestGenerator;

    /** @var KeyCatalogue */
    private $catalogue;

    /** @var TranslationsCompiler */
    private $translationsCompiler;


    /**
     */
    public function __construct (
        CacheInterface $cache,
        TranslatorInterface $translator,
        CacheDigestGenerator $cacheDigestGenerator,
        KeyCatalogue $catalogue,
        TranslationsCompiler $translationsCompiler
    )
    {
        $this->cache = $cache;
        $this->translator = $translator;
        $this->cacheDigestGenerator = $cacheDigestGenerator;
        $this->catalogue = $catalogue;
        $this->translationsCompiler = $translationsCompiler;
    }


    /**
     * Fetches the catalogue for the given language.
     */
    public function fetchCatalogue (string $namespace, string $locale, bool $useCache = true) : CachedCatalogue
    {
        $fetchCallback = function () use ($namespace, $locale)
        {
            $compiledCatalogue = $this->translationsCompiler->compileCatalogue(
                $this->extractCatalogue($namespace, $locale)
            );

            return new CachedCatalogue(
                $this->cacheDigestGenerator->calculateDigest($compiledCatalogue),
                $compiledCatalogue
            );
        };

        $cacheKey = \sprintf(self::CACHE_PREFIX, $namespace, $locale);
        return $useCache
            ? $this->cache->get($cacheKey, $fetchCallback)
            : $fetchCallback();
    }


    /**
     * Freshly extracts the catalogue.
     */
    private function extractCatalogue (string $namespace, string $locale) : array
    {
        if (!$this->translator instanceof TranslatorBagInterface)
        {
            throw new UnexpectedTypeException($this->translator, TranslatorBagInterface::class);
        }

        $catalogue = $this->translator->getCatalogue($locale);
        $patternsByDomain = $this->catalogue->getPatterns($namespace);
        $result = [];
        $this->extractMessages($catalogue, $patternsByDomain, $result);

        return $result;
    }


    /**
     */
    private function extractMessages (MessageCatalogueInterface $catalogue, array $patternsByDomain, array &$result) : void
    {
        // extract fallback catalogues first, so that the more specific catalogues will overwrite the values
        if (null !== ($fallbackCatalogue = $catalogue->getFallbackCatalogue()))
        {
            $this->extractMessages($fallbackCatalogue, $patternsByDomain, $result);
        }

        foreach ($catalogue->getDomains() as $domain)
        {
            $patternsToExtract = $patternsByDomain[$domain] ?? [];

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
    }
}
