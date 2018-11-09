<?php
declare(strict_types=1);

namespace Tardigrades\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('sexy_field');
        $rootNode
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->useAttributeAsKey('name')
            ->prototype('variable')
            ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
