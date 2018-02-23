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

namespace Tardigrades\SectionField\Generator\Writer;

use Composer\Autoload\ClassLoader;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class GeneratorFileWriter
{
    public static function write(
        Writable $writable
    ) {
        $path = self::getPsr4AutoloadDirectoryForNamespace($writable->getNamespace());
        $store = $path . $writable->getFilename();

        try {
            if (\file_exists($store)) {
                $segments = explode(
                    '/',
                    $store
                );
                $filename = end($segments);
                $backupFilename = '~' . $filename . '.bak';
                $backupStore = str_replace($filename, $backupFilename, $store);
                \copy($store, $backupStore);
            }
            \file_put_contents(
                $store,
                $writable->getTemplate()
            );
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }

    public static function getPsr4AutoloadDirectoryForNamespace(string $namespace)
    {
        $find = explode('\\', $namespace)[0];
        $reflector = new \ReflectionClass(ClassLoader::class);
        $vendorDir = \dirname(\dirname($reflector->getFileName()));
        $vendorFile = $vendorDir . '/composer/autoload_psr4.php';
        if (\is_file($vendorFile)) {
            $namespaces = include $vendorFile;
            if (is_array($namespaces)) {
                foreach ($namespaces as $key => $value) {
                    if (strpos($key, $find) === 0) {
                        return str_replace(
                            '\\',
                            '/',
                            $value[0] . str_replace($find, '', $namespace)
                        );
                    }
                }
            }

            throw new \InvalidArgumentException('No path found for ' . $namespace);
        }
        throw new FileNotFoundException();
    }
}
