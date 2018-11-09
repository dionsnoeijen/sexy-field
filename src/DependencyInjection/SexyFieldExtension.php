<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types = 1);

namespace Tardigrades\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Tardigrades\Bundle\SexyFieldBundle\DependencyInjection\Compiler\HTMLPurifierPass;

class SexyFieldExtension extends Extension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator([
                __DIR__ . '/../config/service'
            ])
        );

        try {
            $loader->load('commands.yml');
            $loader->load('services.yml');
        } catch (\Exception $exception) {
            throw $exception;
        }

        array_unshift($configs, [
            'purifier' => [
                'default' => [
                    'Cache.SerializerPath' => '%kernel.cache_dir%/htmlpurifier'
                ]
            ]
        ]);

        $configs = $this->processConfiguration(new Configuration(), $configs);

        $serializerPaths = [];

        foreach ($configs['purifier'] as $name => $config) {

            $configId = "sexy_field.config.$name";
            $configDefinition = $container->register($configId, \HTMLPurifier_Config::class)
                ->setPublic(true)
            ;
            if ('default' === $name) {
                $configDefinition
                    ->setFactory([\HTMLPurifier_Config::class, 'create'])
                    ->addArgument($config)
                ;
            } else {
                $configDefinition
                    ->setFactory([\HTMLPurifier_Config::class, 'inherit'])
                    ->addArgument(new Reference('sexy_field.config.default'))
                    ->addMethodCall('loadArray', [$config])
                ;
            }
            $container->register("sexy_field.$name", \HTMLPurifier::class)
                ->addArgument(new Reference($configId))
                ->setPublic(true)
                ->addTag(HTMLPurifierPass::PURIFIER_TAG, ['profile' => $name])
            ;
            if (isset($config['Cache.SerializerPath'])) {
                $serializerPaths[] = $config['Cache.SerializerPath'];
            }
        }

        $container->setAlias(\HTMLPurifier::class, 'sexy_field.default')
            ->setPublic(true);

        $container->setParameter('sexy_field.cache_warmer.serializer.paths', array_unique($serializerPaths));
    }

    /**
     * {@inheritdoc}
     */
    public function getAlias(): string
    {
        return 'sexy_field';
    }
}
