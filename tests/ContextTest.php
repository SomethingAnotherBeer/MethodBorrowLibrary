<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Tests;

use PHPUnit\Framework\TestCase;
use Somethinganotherbeer\Methodborrow\Context;
use Somethinganotherbeer\Methodborrow\Exception\BuiltInParameterValueNotSpecifiedException;
use Somethinganotherbeer\Methodborrow\Exception\ImplementationNotImplementsSpecifiedClassException;
use Somethinganotherbeer\Methodborrow\Exception\ImplementationNotSpecifiedException;
use Somethinganotherbeer\Methodborrow\Factory\ConfFactory;
use Somethinganotherbeer\Tests\ClassData\ClassOne;
use Somethinganotherbeer\Tests\ClassData\ClassX;

class ContextTest extends TestCase
{
    public function testCallContextSuccessfully(): void
    {
        $configuration = 
        [
            'Somethinganotherbeer\Tests\ClassData\ClassOne' => 
            [
                'implementation_list' =>
                [
                    [
                        'implementation_name' => 'Somethinganotherbeer\Tests\ClassData\DataInterface',
                        'implementation_value' => 'Somethinganotherbeer\Tests\ClassData\ClassThree',
                    ],
                ],
                'replacement_list' =>
                [
                    [
                        'replacement_name' => 'built_in_value',
                        'replacement_value' => 5,
                    ],
                ],
            ],
        ];

        $confFactory = ConfFactory::makeInstance();
        $classConfList = $confFactory->makeClassConfList($configuration);
        $context = Context::makeInstance($classConfList);

        $contextMethod = $context->borrowMethodFromClass(ClassOne::class, 'doSomething');
        $this->assertSame(170, $contextMethod(5));

    }

    public function testBuiltInParameterValueNotSpecified(): void
    {
        $this->expectException(BuiltInParameterValueNotSpecifiedException::class);
        $this->expectExceptionMessage("Следующие параметры со встроенным типом не имеют дефолтных значений в конфигурации: built_in_value");

         $configuration = 
        [
            'Somethinganotherbeer\Tests\ClassData\ClassOne' => 
            [
                'implementation_list' =>
                [
                    [
                        'implementation_name' => 'Somethinganotherbeer\Tests\ClassData\DataInterface',
                        'implementation_value' => 'Somethinganotherbeer\Tests\ClassData\ClassThree',
                    ],
                ],
                
            ],
        ];

        $confFactory = ConfFactory::makeInstance();
        $classConfList = $confFactory->makeClassConfList($configuration);
        $context = Context::makeInstance($classConfList);

        $context->borrowMethodFromClass(ClassOne::class, 'doSomething');

    }

    public function testImplementationNotSpecified(): void
    {
        $this->expectException(ImplementationNotSpecifiedException::class);
        $this->expectExceptionMessage("В конфигурации не указана реализация для абстрактного класса/интерфейса с именем Somethinganotherbeer\Tests\ClassData\DataInterface");

        $configuration = 
        [
            'Somethinganotherbeer\Tests\ClassData\ClassOne' => 
            [
                'replacement_list' =>
                [
                    [
                        'replacement_name' => 'built_in_value',
                        'replacement_value' => 5,
                    ],
                ],
            ]
        ];

        $confFactory = ConfFactory::makeInstance();
        $classConfList = $confFactory->makeClassConfList($configuration);
        $context = Context::makeInstance($classConfList);

        $context->borrowMethodFromClass(ClassOne::class, 'doSomething');
    }

    public function testImplementationNotImplementsSpecifiedClass(): void
    {
        $this->expectException(ImplementationNotImplementsSpecifiedClassException::class);
        $this->expectExceptionMessage("Указанный в конфигурации класс с именем Somethinganotherbeer\Tests\ClassData\ClassY не реализует абстрактный класс/интерфейс с именем Somethinganotherbeer\Tests\ClassData\YInterface");

        $configuration = 
        [
            'Somethinganotherbeer\Tests\ClassData\ClassX' => 
            [
                'implementation_list' =>
                [
                    [
                        'implementation_name' => 'Somethinganotherbeer\Tests\ClassData\YInterface',
                        'implementation_value' => 'Somethinganotherbeer\Tests\ClassData\ClassY',
                    ],
                ],
            ]
        ];

        $confFactory = ConfFactory::makeInstance();
        $classConfList = $confFactory->makeClassConfList($configuration);
        $context = Context::makeInstance($classConfList);

        $context->borrowMethodFromClass(ClassX::class, 'simpleDo');


    }


}