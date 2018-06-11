<?php
declare(strict_types=1);

namespace Tardigrades\SectionField\ValueObject;


interface ConfigWithHandleInterface extends ConfigInterface
{
    public function getHandle(): Handle;
}
