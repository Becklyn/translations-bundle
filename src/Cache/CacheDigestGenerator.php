<?php declare(strict_types=1);

namespace Becklyn\Translations\Cache;

class CacheDigestGenerator
{
    /**
     * @var int
     */
    private $version;


    /**
     * @param int $version
     */
    public function __construct (int $version)
    {
        $this->version = $version;
    }


    /**
     * @param string $catalogue
     *
     * @return string
     */
    public function calculateDigest (string $catalogue) : string
    {
        return \sha1($catalogue) . "-{$this->version}";
    }
}
