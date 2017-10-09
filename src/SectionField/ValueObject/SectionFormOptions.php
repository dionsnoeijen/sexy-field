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

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class SectionFormOptions
{
    /** @var $options */
    private $options;

    private function __construct(array $options)
    {
        $this->options = $options;
    }

    public function getId(): Id
    {
        Assertion::keyIsset($this->options, 'id', 'The id is not set');
        Assertion::digit($this->options['id'], 'The id must be a digit');

        return Id::fromInt((int) $this->options['id']);
    }

    public function getSlug(): Slug
    {
        Assertion::keyIsset($this->options, 'slug', 'The slug is not set');
        Assertion::string($this->options['slug'], 'The slug must be a string');
        Assertion::notEmpty($this->options['slug'], 'The slug is empty');

        return Slug::fromString($this->options['slug']);
    }

    public function getRedirect(): string
    {
        Assertion::keyIsset($this->options, 'redirect', 'The redirect is not set');
        Assertion::string($this->options['redirect'], 'The redirect must be a string');

        return $this->options['redirect'];
    }

    public static function fromArray(array $options): self
    {
        return new self($options);
    }
}
