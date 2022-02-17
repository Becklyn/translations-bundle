<?php declare(strict_types=1);

namespace Becklyn\Translations\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class BecklynTranslationsConfiguration implements ConfigurationInterface
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder () : TreeBuilder
    {
        $treeBuilder = new TreeBuilder("becklyn_translations");
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode("extract")
                    ->info("All the translation domains + keys that should be extracted, grouped by namespace. Can use * as placeholders.")
                    ->normalizeKeys(false)
                    ->arrayPrototype()
                        ->arrayPrototype()
                            ->scalarPrototype()
                                ->validate()
                                ->ifTrue(function ($value) { return !\is_string($value); })
                                    ->thenInvalid("Must pass strings as translation keys.")
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->integerNode("cache_version")
                    ->info("Normally the cache key generation is fully automatic, but if there is an issue, you can just bump the cache key version here.")
                    ->defaultValue(1)
                ->end()
            ->end();

        return $treeBuilder;
    }
}
