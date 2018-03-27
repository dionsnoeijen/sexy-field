<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Generator\PhpFormatter
 */
final class PhpFormatterTest extends TestCase
{
    /**
     * @test
     * @covers ::format
     */
    public function it_should_format()
    {
        $inputText = <<<'TXT'
<?php
declare (strict_types=1);
namespace AppBundle\Entity;
use Tardigrades;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
class Project
{
    /** @var \DateTime */
protected $created;
/** @var \DateTime */
protected $updated;
/** @var Organisation */
protected $organisation;
/** @var string */
protected $projectName;
/** @var string */
protected $projectSlug;
/** @var ArrayCollection */
protected $invitants;
    /** @var int */
    private $id;
    public function __construct()
    {
        $this->invitants = new ArrayCollection();
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getCreated(): ?\DateTime
{
    return $this->created;
}
public function setCreated(\DateTime $created): Project
{
    $this->created = $created;
    return $this;
}
public function getUpdated(): ?\DateTime
{
    return $this->updated;
}
public function setUpdated(\DateTime $updated): Project
{
    $this->updated = $updated;
    return $this;
}
public function getOrganisation(): ?Organisation
{
    return $this->organisation;
}
public function hasOrganisation(): bool
{
    return !empty($this->organisation);
}
public function setOrganisation(Organisation $organisation): Project
{
    $this->organisation = $organisation;
    return $this;
}
public function removeOrganisation(): Project
{
    $this->organisation = null;
    return $this;
}
public function getProjectName(): ?string
{
    return $this->projectName;
}
public function setProjectName(string $projectName): Project
{
    $this->projectName = $projectName;
    return $this;
}
public function getProjectSlug(): ?Tardigrades\SectionField\ValueObject\Slug
{
    if (!empty($this->projectSlug)) {
        return Tardigrades\SectionField\ValueObject\Slug::fromString($this->projectSlug);
    }
    return null;
}
public function getInvitants(): Collection
{
    return $this->invitants;
}
public function addInvitant(Invitant $invitant): Project
{
    if ($this->invitants->contains($invitant)) {
        return $this;
    }
    $this->invitants->add($invitant);
        $invitant->setProject($this);
    return $this;
}
public function removeInvitant(Invitant $invitant): Project
{
    if (!$this->invitants->contains($invitant)) {
        return $this;
    }
    $this->invitants->removeElement($invitant);
        $invitant->removeProject($this);
    return $this;
}
    public function getSlug(): Tardigrades\SectionField\ValueObject\Slug
{
    return Tardigrades\SectionField\ValueObject\Slug::fromString($this->projectSlug);
}
    public function getDefault(): string
{
    return $this->projectName;
}
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('projectName', new Assert\Length(['min' => '2','max' => '255']));
        $metadata->addPropertyConstraint('projectName', new Assert\NotBlank());
    }
    public function onPrePersist(): void
    {
        $this->created = new \DateTime('now');
$this->updated = new \DateTime('now');
$this->projectSlug = Tardigrades\Helper\StringConverter::toSlug($this->getProjectName() . '-' . $this->getCreated()->format('Y-m-d'));
    }
    public function onPreUpdate(): void
    {
        $this->updated = new \DateTime('now');
    }
}


TXT;

        $outputText = <<<'TXT'
<?php
declare (strict_types=1);

namespace AppBundle\Entity;

use Tardigrades;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class Project
{
    /** @var \DateTime */
    protected $created;

    /** @var \DateTime */
    protected $updated;

    /** @var Organisation */
    protected $organisation;

    /** @var string */
    protected $projectName;

    /** @var string */
    protected $projectSlug;

    /** @var ArrayCollection */
    protected $invitants;

    /** @var int */
    private $id;

    public function __construct()
    {
        $this->invitants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreated(): ?\DateTime
    {
        return $this->created;
    }

    public function setCreated(\DateTime $created): Project
    {
        $this->created = $created;
        return $this;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(\DateTime $updated): Project
    {
        $this->updated = $updated;
        return $this;
    }

    public function getOrganisation(): ?Organisation
    {
        return $this->organisation;
    }

    public function hasOrganisation(): bool
    {
        return !empty($this->organisation);
    }

    public function setOrganisation(Organisation $organisation): Project
    {
        $this->organisation = $organisation;
        return $this;
    }

    public function removeOrganisation(): Project
    {
        $this->organisation = null;
        return $this;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }

    public function setProjectName(string $projectName): Project
    {
        $this->projectName = $projectName;
        return $this;
    }

    public function getProjectSlug(): ?Tardigrades\SectionField\ValueObject\Slug
    {
        if (!empty($this->projectSlug)) {
            return Tardigrades\SectionField\ValueObject\Slug::fromString($this->projectSlug);
        }
        return null;
    }

    public function getInvitants(): Collection
    {
        return $this->invitants;
    }

    public function addInvitant(Invitant $invitant): Project
    {
        if ($this->invitants->contains($invitant)) {
            return $this;
        }
        $this->invitants->add($invitant);
        $invitant->setProject($this);
        return $this;
    }

    public function removeInvitant(Invitant $invitant): Project
    {
        if (!$this->invitants->contains($invitant)) {
            return $this;
        }
        $this->invitants->removeElement($invitant);
        $invitant->removeProject($this);
        return $this;
    }

    public function getSlug(): Tardigrades\SectionField\ValueObject\Slug
    {
        return Tardigrades\SectionField\ValueObject\Slug::fromString($this->projectSlug);
    }

    public function getDefault(): string
    {
        return $this->projectName;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('projectName', new Assert\Length(['min' => '2','max' => '255']));
        $metadata->addPropertyConstraint('projectName', new Assert\NotBlank());
    }

    public function onPrePersist(): void
    {
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
        $this->projectSlug = Tardigrades\Helper\StringConverter::toSlug($this->getProjectName() . '-' . $this->getCreated()->format('Y-m-d'));
    }

    public function onPreUpdate(): void
    {
        $this->updated = new \DateTime('now');
    }
}

TXT;

        $result = PhpFormatter::format($inputText);

        $this->assertSame($outputText, $result);
    }
}
