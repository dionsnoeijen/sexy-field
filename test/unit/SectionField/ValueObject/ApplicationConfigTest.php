<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;
use Tardigrades\Helper\ArrayConverter;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\ApplicationConfig
 * @covers ::<private>
 * @covers ::__construct
 */
class ApplicationConfigTest extends TestCase
{
    /**
     * @test
     * @covers ::fromArray
     * @covers ::toArray
     */
    public function it_should_create()
    {
        $appConfig = [
            'application' => [
                'name' => 'sexy',
                'handle' => 'field',
                'languages' => [
                    'this one', 'that other one'
                ]
            ]
        ];
        $applicationConfig = ApplicationConfig::fromArray($appConfig);
        $this->assertInstanceOf(ApplicationConfig::class, $applicationConfig);
        $this->assertSame($applicationConfig->toArray(), $appConfig);
    }

    /**
     * @test
     * @covers ::fromArray
     */
    public function it_should_throw_exception_if_keys_are_wrong()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The name for the application is required.');
        $appConfig = [
            'application' => [
                'handle' => 'field',
                'languages' => [
                    'this one', 'that other one'
                ]
            ]
        ];
        ApplicationConfig::fromArray($appConfig);

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('The handle for the application is required.');
        $appConfig = [
            'application' => [
                'name' => 'field',
                'languages' => [
                    'this one', 'that other one'
                ]
            ]
        ];
        ApplicationConfig::fromArray($appConfig);

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Languages should contain an array');
        $appConfig = [
            'application' => [
                'name' => 'wheeee',
                'handle' => 'field',
                'languages' => 'everything'
            ]
        ];
        ApplicationConfig::fromArray($appConfig);

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Languages should contain an array');
        $appConfig = [
            'application' => [
                'name' => 'wheeee',
                'handle' => 'field'
            ]
        ];
        ApplicationConfig::fromArray($appConfig);

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Config is not a application config');
        $appConfig = [
            'alpication' => [
                'name' => 'wheeee',
                'handle' => 'field',
                'languages' => 'everything'
            ]
        ];
        ApplicationConfig::fromArray($appConfig);
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $appConfig = [
            'application' => [
                'name' => 'sexy',
                'handle' => 'field',
                'languages' => [
                    'this one', 'that other one'
                ]
            ]
        ];

        $applicationConfigString = (string)ApplicationConfig::fromArray($appConfig);
        $appConfigString = ArrayConverter::recursive($appConfig);
        $this->assertSame($applicationConfigString, $appConfigString);
    }
}
