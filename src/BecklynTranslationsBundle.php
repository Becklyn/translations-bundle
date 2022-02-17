<?php declare(strict_types=1);

namespace Becklyn\Translations;

use Becklyn\Translations\Cache\CacheDigestGenerator;
use Becklyn\Translations\Catalogue\KeyCatalogue;
use Becklyn\Translations\DependencyInjection\BecklynTranslationsConfiguration;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class BecklynTranslationsBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function getContainerExtension () : ?ExtensionInterface
    {
        return new class() extends Extension {
            /**
             * @inheritdoc
             */
            public function load (array $configs, ContainerBuilder $container) : void
            {
                // load services
                $loader = new YamlFileLoader(
                    $container,
                    new FileLocator(__DIR__ . "/Resources/config")
                );
                $loader->load("services.yaml");

                $config = $this->processConfiguration(new BecklynTranslationsConfiguration(), $configs);

                $container->getDefinition(KeyCatalogue::class)
                    ->addMethodCall("addNamespaceGrouped", [$config["extract"]]);

                $container->getDefinition(CacheDigestGenerator::class)
                    ->setArgument('$version', $config["cache_version"]);
            }


            /**
             * @inheritDoc
             */
            public function getAlias () : string
            {
                return "becklyn_translations";
            }
        };
    }
}
