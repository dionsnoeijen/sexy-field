<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\LanguageConfig
 * @covers ::<private>
 * @covers ::__construct
 */
class LanguageConfigTest extends TestCase
{
    /**
     * @test
     * @covers ::fromArray
     * @covers ::toArray
     */
    public function it_should_create_from_array()
    {
        $array = [
            'yadayada' => 'does not matter',
            'language' => ['this language, this other too']
        ];
        $sexyLanguageConfig = LanguageConfig::fromArray($array);
        $this->assertInstanceOf(LanguageConfig::class, $sexyLanguageConfig);
        $this->assertSame($array, $sexyLanguageConfig->toArray());
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_language_is_not_present()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Config is not a language config');
        $array = [
            'yadayada' => 'does not matter',
        ];
        LanguageConfig::fromArray($array);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_when_language_is_not_array()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The languages should consist of an array');
        $array = [
            'language' => 'not a language',
        ];
        LanguageConfig::fromArray($array);
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $application['language'] = ['nl_NL', 'en_EN'];
        $languageConfigString = (string)LanguageConfig::fromArray($application);
        $this->assertSame("0:nl_NL\n1:en_EN\n", $languageConfigString);
    }
}
