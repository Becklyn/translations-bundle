<?php declare(strict_types=1);

namespace Becklyn\Translations\DependencyInjection;

use Becklyn\Translations\Catalogue\KeyCatalogue;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtractTranslationsPass implements CompilerPassInterface
{
    /**
     * @var array
     */
    private $map;


    /**
     * @param array $map
     */
    public function __construct (array $map)
    {
        $this->map = $map;
    }


    /**
     * @param ContainerBuilder $container
     */
    public function process (ContainerBuilder $container) : void
    {
        $container->getDefinition(KeyCatalogue::class)
            ->addMethodCall("add", [$this->map]);
    }
}
