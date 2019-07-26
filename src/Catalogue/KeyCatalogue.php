<?php declare(strict_types=1);

namespace Becklyn\Translations\Catalogue;

/**
 *
 */
class KeyCatalogue
{
    /**
     * @var string[][][]
     */
    private $domains = [];


    /**
     * @param string $domain
     * @param array  $patterns
     */
    public function add (string $namespace, string $domain, array $patterns) : void
    {
        foreach ($patterns as $pattern)
        {
            $this->domains[$namespace][$domain][] = $pattern;
        }
    }


    /**
     * @param array $map
     */
    public function addDomainGrouped (string $namespace, array $map) : void
    {
        foreach ($map as $domain => $patterns)
        {
            $this->add($namespace, $domain, $patterns);
        }
    }


    /**
     * @param array $map
     */
    public function addNamespaceGrouped (array $map) : void
    {
        foreach ($map as $namespace => $domainGrouped)
        {
            $this->addDomainGrouped($namespace, $domainGrouped);
        }
    }


    /**
     * @return array
     */
    public function getPatterns (string $namespace) : array
    {
        $result = [];

        foreach (($this->domains[$namespace] ?? []) as $domain => $patterns)
        {
            $entry = [];

            foreach ($patterns as $pattern)
            {
                $pattern = \preg_replace('~\\*\\*+~', '*', $pattern);

                $entry[] = '/^' . \implode(
                    ".*?",
                    \array_map('preg_quote', \explode("*", $pattern))
                ) . '$/';
            }

            $result[$domain] = $entry;
        }

        return $result;
    }
}
