<?php declare(strict_types=1);

namespace Becklyn\Translations\Catalogue;

/**
 *
 */
class KeyCatalogue
{
    /**
     * @var string[][]
     */
    private $domains = [];


    /**
     * @param string $domain
     * @param array  $patterns
     */
    public function add (string $domain, array $patterns) : void
    {
        foreach ($patterns as $pattern)
        {
            $this->domains[$domain][] = $pattern;
        }
    }


    /**
     * @param array $map
     */
    public function addAll (array $map) : void
    {
        foreach ($map as $domain => $patterns)
        {
            $this->add($domain, $patterns);
        }
    }


    /**
     * @return array
     */
    public function getPatterns () : array
    {
        $result = [];

        foreach ($this->domains as $domain => $patterns)
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
