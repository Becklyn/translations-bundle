<?php declare(strict_types=1);

namespace Becklyn\Translations\Catalogue;

class CachedCatalogue
{
    /**
     * @var string
     */
    private $hash;


    /**
     * @var string
     */
    private $compiledCatalogue;


    /**
     */
    public function __construct (string $hash, string $compiledCatalogue)
    {
        $this->hash = $hash;
        $this->compiledCatalogue = $compiledCatalogue;
    }


    /**
     */
    public function getHash () : string
    {
        return $this->hash;
    }


    /**
     */
    public function getCompiledCatalogue () : string
    {
        return $this->compiledCatalogue;
    }
}
