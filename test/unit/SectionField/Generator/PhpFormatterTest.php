<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Generator\PhpFormatter
 * @covers ::__construct
 * @covers ::<protected>
 */
final class PhpFormatterTest extends TestCase
{
    /**
     * @test
     */
    public function it_should_format()
    {
        $inputText = <<<TXT
<?php
declare (strict_types=1);
namespace {{ namespace }};
use Tardigrades;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
{{ use }}
class {{ section }}
{
    {{ properties }}

    /** @var int */
    private \$id;
    public function __construct()
    {
        {{ constructor }}
    }

    public function getId(): ?int
    {
        return \$this->id;
    }

    {{ methods }}
    {{ getSlug }}
    {{ getDefault }}

    public static function loadValidatorMetadata(ClassMetadata \$metadata)
    {
        {{ validatorMetadata }}
        {{ validatorMetadataSectionPhase }}
    }

    public function onPrePersist(): void
    {
        {{ prePersist }}
    }

    public function onPreUpdate(): void
    {
        {{ preUpdate }}
    }
}

TXT;

        $outputText = <<<TXT
<?php
declare (strict_types=1);

namespace {{ namespace }};

use Tardigrades;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
{{ use }}

class {{ section }}
{
{{ properties }}
    /** @var int */
    private \$id;

    public function __construct()
    {
    {{ constructor }}
    }

    public function getId(): ?int
    {
        return \$this->id;
    }
{{ methods }}
{{ getSlug }}
{{ getDefault }}

    public static function loadValidatorMetadata(ClassMetadata \$metadata)
    {
    {{ validatorMetadata }}
    {{ validatorMetadataSectionPhase }}
    }

    public function onPrePersist(): void
    {
    {{ prePersist }}
    }

    public function onPreUpdate(): void
    {
    {{ preUpdate }}
    }
}


TXT;


        $result = PhpFormatter::format($inputText);

        $this->assertSame($outputText, $result);
    }
}






