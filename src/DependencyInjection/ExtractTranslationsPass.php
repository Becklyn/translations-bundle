<?php declare(strict_types=1);

namespace Becklyn\Translations\DependencyInjection;

use Becklyn\Translations\Catalogue\KeyCatalogue;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ExtractTranslationsPass implements CompilerPassInterface
{
    private array $namespaceGroupedMap;


    public function __construct (array $namespaceGroupedMap)
    {
        $this->namespaceGroupedMap = $namespaceGroupedMap;
    }


    public function process (ContainerBuilder $container) : void
    {
        $container->getDefinition(KeyCatalogue::class)
            ->addMethodCall("addNamespaceGrouped", [$this->namespaceGroupedMap]);
    }
}
