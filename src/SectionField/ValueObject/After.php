<?php

/*
 * This file is part of the SexyField package.
 *
 * (c) Dion Snoeijen <hallo@dionsnoeijen.nl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use Assert\Assertion;

final class After
{
    /** @var \DateTimeInterface */
    private $dateTime;

    private function __construct(\DateTimeInterface $dateTime)
    {
        $this->dateTime = $dateTime;
    }

    public function __toString()
    {
        return $this->dateTime->format('D-m-y h:i');
    }

    public static function fromString(string $dateTime): self
    {
        $format = 'D-m-y h:i';
        Assertion::date($dateTime, $format);
        $dateTime = \DateTimeImmutable::createFromFormat($format, $dateTime);

        return new self($dateTime);
    }

    public static function fromDateTime(\DateTime $dateTime): self
    {
        return new self(\DateTimeImmutable::createFromMutable($dateTime));
    }
}
