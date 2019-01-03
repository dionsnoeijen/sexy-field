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

class Writable
{
    /** @var string */
    private $template;

    /** @var string */
    private $namespace;

    /** @var string */
    private $filename;

    /** @var bool */
    private $clobber;

    private function __construct(
        string $template,
        string $namespace,
        string $filename,
        bool $clobber = true
    ) {
        $this->template = $template;
        $this->namespace = $namespace;
        $this->filename = $filename;
        $this->clobber = $clobber;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function shouldClobber(): bool
    {
        return $this->clobber;
    }

    public static function create(
        string $template,
        string $namespace,
        string $filename,
        bool $clobber = true
    ): self {
        return new static($template, $namespace, $filename, $clobber);
    }
}
