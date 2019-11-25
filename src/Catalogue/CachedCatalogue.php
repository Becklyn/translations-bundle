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
    private $catalogueJson;


    /**
     */
    public function __construct (string $hash, string $catalogueJson)
    {
        $this->hash = $hash;
        $this->catalogueJson = $catalogueJson;
    }


    /**
     */
    public function getHash () : string
    {
        return $this->hash;
    }


    /**
     */
    public function getCatalogueJson () : string
    {
        return $this->catalogueJson;
    }
}
