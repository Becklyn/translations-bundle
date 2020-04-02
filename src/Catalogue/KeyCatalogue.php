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
     */
    public function add (string $namespace, string $domain, array $patterns) : void
    {
        foreach ($patterns as $pattern)
        {
            $this->domains[$namespace][$domain][] = $pattern;
        }
    }


    /**
     */
    public function addDomainGrouped (string $namespace, array $map) : void
    {
        foreach ($map as $domain => $patterns)
        {
            $this->add($namespace, $domain, $patterns);
        }
    }


    /**
     */
    public function addNamespaceGrouped (array $map) : void
    {
        foreach ($map as $namespace => $domainGrouped)
        {
            $this->addDomainGrouped($namespace, $domainGrouped);
        }
    }


    /**
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


    /**
     * @return string[]
     */
    public function getNamespaces () : array
    {
        return \array_keys($this->domains);
    }
}
