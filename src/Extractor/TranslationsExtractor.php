<?php declare(strict_types=1);

namespace Becklyn\Translations\Extractor;

use Becklyn\Cache\Cache\SimpleCacheFactory;
use Becklyn\Cache\Cache\SimpleCacheItemInterface;
use Becklyn\Translations\Cache\CacheDigestGenerator;
use Becklyn\Translations\Catalogue\CachedCatalogue;
use Becklyn\Translations\Catalogue\KeyCatalogue;
use Becklyn\Translations\Exception\TranslationsCompilationFailedException;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Config\Resource\ResourceInterface;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TranslationsExtractor
{
    private const CACHE_KEY = "becklyn.javascript_translations.dump_%s_%s";

    /** @var TranslatorInterface&TranslatorBagInterface */
    private $translator;

    /** @var CacheDigestGenerator */
    private $cacheDigestGenerator;

    /** @var KeyCatalogue */
    private $catalogue;

    /** @var TranslationsCompiler */
    private $translationsCompiler;

    /** @var KernelInterface */
    private $kernel;

    /** @var SimpleCacheFactory */
    private $cacheFactory;


    /**
     */
    public function __construct (
        TranslatorInterface $translator,
        CacheDigestGenerator $cacheDigestGenerator,
        KeyCatalogue $catalogue,
        TranslationsCompiler $translationsCompiler,
        SimpleCacheFactory $cacheFactory,
        KernelInterface $kernel
    )
    {
        if (!$translator instanceof TranslatorBagInterface)
        {
            throw new TranslationsCompilationFailedException(\sprintf(
                "Can only extract messages from translator with translator bag, but '%s' given.",
                \get_class($translator)
            ));
        }

        $this->translator = $translator;
        $this->cacheDigestGenerator = $cacheDigestGenerator;
        $this->catalogue = $catalogue;
        $this->translationsCompiler = $translationsCompiler;
        $this->kernel = $kernel;
        $this->cacheFactory = $cacheFactory;
    }


    /**
     * Fetches the catalogue for the given language.
     */
    public function fetchCatalogue (string $namespace, string $locale) : CachedCatalogue
    {
        return $this->getCacheItem($namespace, $locale)->get();
    }


    /**
     *
     */
    private function getCacheItem (string $namespace, string $locale) : SimpleCacheItemInterface
    {
        return $this->cacheFactory->getItem(
            \sprintf(self::CACHE_KEY, $namespace, $locale),
            function () use ($namespace, $locale)
            {
                $compiledCatalogue = $this->translationsCompiler->compileCatalogue(
                    $this->extractCatalogue($namespace, $locale)
                );

                return new CachedCatalogue(
                    $this->cacheDigestGenerator->calculateDigest($compiledCatalogue),
                    $compiledCatalogue
                );
            },
            $this->getTrackedResources($locale)
        );
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


    /**
     * @return ResourceInterface[]
     */
    private function getTrackedResources (string $locale) : array
    {
        $resources = $this->translator->getCatalogue($locale)->getResources();

        // we also need to add all bundle classes as resource, as they define the exported
        // messages
        foreach ($this->kernel->getBundles() as $bundle)
        {
            $filePath = (new \ReflectionObject($bundle))->getFileName();

            if ($filePath)
            {
                $resources[] = new FileResource($filePath);
            }
        }

        return $resources;
    }


    /**
     * Resets + warms up the cache for the given combination.
     */
    public function resetCache (string $namespace, string $locale)
    {
        $this->getCacheItem($namespace, $locale)->warmup();
    }
}
