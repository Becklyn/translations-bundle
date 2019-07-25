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
     * @param string $hash
     * @param string $catalogueJson
     */
    public function __construct (string $hash, string $catalogueJson)
    {
        $this->hash = $hash;
        $this->catalogueJson = $catalogueJson;
    }


    /**
     * @return string
     */
    public function getHash () : string
    {
        return $this->hash;
    }


    /**
     * @return string
     */
    public function getCatalogueJson () : string
    {
        return $this->catalogueJson;
    }
}
