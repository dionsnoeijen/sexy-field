<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\Generator;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\Generator\XmlFormatter
 */
final class XmlFormatterTest extends TestCase
{
    /**
     * @test
     * @covers ::format
     */
    public function it_should_format()
    {
        $inputText = <<<'TXT'
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
                  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                  xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping http://raw.github.com/doctrine/doctrine2/master/doctrine-mapping.xsd">

    <entity
        name="The entity"
        table="theEntity"
    >
        <lifecycle-callbacks>
            <lifecycle-callback type="prePersist" method="onPrePersist" />
            <lifecycle-callback type="preUpdate" method="onPreUpdate" />
        </lifecycle-callbacks>

        <id name="id" type="integer">
            <generator strategy="AUTO" />
        </id>

        <field name="fieldOne" nullable="true" type="string" />
        <field name="fieldTwo" nullable="true" type="string" />

        <one-to-one field="fieldTwo" target-entity="\My\Namespace\ClassTwo" mapped-by="ClassOne" />
        </entity>
        <entity name="\My\Namespace\ClassTwo">
        <one-to-one field="fieldOne" target-entity="\My\Namespace\ClassOne" inversed-by="ClassTwo">
            <join-column name="fieldTwo_id" referenced-column-name="id" />
        </one-to-one>

    </entity>
</doctrine-mapping>

TXT;

        $outputText = <<<'TXT'
<?xml version="1.0"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping http://raw.github.com/doctrine/doctrine2/master/doctrine-mapping.xsd">
  <entity name="The entity" table="theEntity">
    <lifecycle-callbacks>
      <lifecycle-callback type="prePersist" method="onPrePersist"/>
      <lifecycle-callback type="preUpdate" method="onPreUpdate"/>
    </lifecycle-callbacks>
    <id name="id" type="integer">
      <generator strategy="AUTO"/>
    </id>
    <field name="fieldOne" nullable="true" type="string"/>
    <field name="fieldTwo" nullable="true" type="string"/>
    <one-to-one field="fieldTwo" target-entity="\My\Namespace\ClassTwo" mapped-by="ClassOne"/>
  </entity>
  <entity name="\My\Namespace\ClassTwo">
    <one-to-one field="fieldOne" target-entity="\My\Namespace\ClassOne" inversed-by="ClassTwo">
      <join-column name="fieldTwo_id" referenced-column-name="id"/>
    </one-to-one>
  </entity>
</doctrine-mapping>

TXT;

        $result = XMLFormatter::format($inputText);

        $this->assertSame($outputText, $result);
    }
}
