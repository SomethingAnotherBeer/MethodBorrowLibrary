<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Tests;

use PHPUnit\Framework\TestCase;
use Somethinganotherbeer\Methodborrow\Checker\ConfParamsChecker;
use Somethinganotherbeer\Methodborrow\Exception\ConfClassKeysIsNumericException;
use Somethinganotherbeer\Methodborrow\Exception\ClassNameStartsWithDigitException;
use Somethinganotherbeer\Methodborrow\Exception\ClassNameHasDashException;
use Somethinganotherbeer\Methodborrow\Exception\ConfClassKeyNotReferenceOnArrayException;
use Somethinganotherbeer\Methodborrow\Exception\ImplementationConfException;
use Somethinganotherbeer\Methodborrow\Exception\ReplacementConfException;

class ConfCheckerTest extends TestCase
{
    public function testConfClassListHasIntKeys(): void
    {
        $this->expectException(ConfClassKeysIsNumericException::class);
        $conf_example = 
        [
            "Somespace\other" => [],
            1 => [],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);

    }

    public function testConfClassListStartsWithDigit(): void
    {
        $this->expectException(ClassNameStartsWithDigitException::class);
        $conf_example = 
        [
            "Somespace\other" => [],
            "11Somespace\other" => [],
        ];
        
        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }

    public function testConfClassListHasDash(): void
    {
        $this->expectException(ClassNameHasDashException::class);
        $conf_example = 
        [
            "Somespace\other" => [],
            "Somespace\other-space" => [],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }

    public function testConfClassListNotReferenceOnArray(): void
    {
        $this->expectException(ConfClassKeyNotReferenceOnArrayException::class);
        $conf_example = 
        [
            "Somespace\other" => "some string",
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }


    public function testImplementationConfImplementationConfNotArray(): void
    {
        $classname = "Somenamespace\Simpleclass";
        $this->expectException(ImplementationConfException::class);
        $this->expectExceptionMessage("Значение implementation_list для класса с наименованием $classname не является массивом");

        $conf_example =
        [
            $classname => ['implementation_list' => 'some string'],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);


    }

    public function testImplementationConfImplementationConfItemNotArray(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ImplementationConfException::class);
        $this->expectExceptionMessage("Конфигурация реализации для класса с наименованием $classname в строке 1 не является массивом");

        $conf_example =
        [
            $classname => ['implementation_list' => ['some_string']],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }

    public function testImplementationConfImplementationHasNotImplementationName(): void
    {
        $classname = "Somenamespace\Simpleclass";
        $this->expectException(ImplementationConfException::class);
        $this->expectExceptionMessage("Конфигурация реализации для класса с наименованием $classname в строке 1 не содержит ключа implementation_name");

         $conf_example =
        [
            $classname => ["implementation_list" => [['implementation_value' => 'Somenamespace\OtherClass']]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }

    public function testImplementationConfImplementationHasNotImplementationValue(): void
    {
        $classname = "Somenamespace\Simpleclass";
        $this->expectException(ImplementationConfException::class);
        $this->expectExceptionMessage("Конфигурация реализации для класса с наименованием $classname в строке 1 не содержит ключа implementation_value");

         $conf_example =
        [
            $classname => ["implementation_list" => [['implementation_name' => 'Somenamespace\OtherClass']]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }

    public function testImplementationConfImplementationNameIsNotString(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ImplementationConfException::class);
        $this->expectExceptionMessage("Конфигурация реализации для класса с наименованием $classname в строке 1 содержит нестроковый тип для значения по ключу implementation_name");

         $conf_example =
        [
            $classname => ["implementation_list" => [['implementation_name' => 111, 'implementation_value' => "Someother\OtherClass"]]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);

    }

    public function testImplementationConfImplementationValueIsNotString(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ImplementationConfException::class);
        $this->expectExceptionMessage("Конфигурация реализации для класса с наименованием $classname в строке 1 содержит нестроковый тип для значения по ключу implementation_value");

         $conf_example =
        [
            $classname => ["implementation_list" => [['implementation_value' => 111, 'implementation_name' => "Someother\OtherClass"]]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }

    public function testImplementationConfImplementationNameStartsWithDigit(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ImplementationConfException::class);
        $this->expectExceptionMessage("Конфигурация реализации для класса с наименованием $classname в строке 1 по ключу implementation_name содержит наименование класса, которое начинается с цифры");

         $conf_example =
        [
            $classname => ["implementation_list" => [['implementation_name' => "111Some\Other", 'implementation_value' => "Someother\OtherClass"]]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }

    public function testImplementationConfImplementationNameHasDash(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ImplementationConfException::class);
        $this->expectExceptionMessage("Конфигурация реализации для класса с наименованием $classname в строке 1 по ключу implementation_name содержит наименование класса, которое содержит тире");

        $conf_example =
        [
            $classname => ["implementation_list" => [['implementation_name' => "Some\Other-Space", 'implementation_value' => "Someother\OtherClass"]]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);

    }

    public function testImplementationConfImplementationValueStartsWithDigit(): void
    {
        $classname = "Somenamespace\Simpleclass";
        $this->expectException(ImplementationConfException::class);
        $this->expectExceptionMessage("Конфигурация реализации для класса с наименованием $classname в строке 1 по ключу implementation value содержит наименование класса, которое начинается с цифры");

        $conf_example =
        [
            $classname => ["implementation_list" => [['implementation_name' => "Some\OtherSpace", 'implementation_value' => "111Someother\OtherClass"]]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);

    }

    public function testImplementationConfImplementationValueHasDash(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ImplementationConfException::class);
        $this->expectExceptionMessage("Конфигурация реализации для класса с наименованием $classname в строке 1 по ключу implementation_value содержит наименование класса, которое содержит тире");

        $conf_example =
        [
            $classname => ["implementation_list" => [['implementation_name' => "Some\OtherSpace", 'implementation_value' => "Someother\Other-Class"]]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }


    public function testReplacementConfNotArray(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ReplacementConfException::class);
        $this->expectExceptionMessage("Значение replacement_list для класса с наименованием $classname не является массивом");

        $conf_example =
        [
            $classname => ["replacement_list" => "some string"],            
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }

    public function testReplacementConfItemNotArray(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ReplacementConfException::class);
        $this->expectExceptionMessage("Конфигурация значений по умолчанию для класса с наименованием $classname в строке 1 не является массивом");

        $conf_example = 
        [
            $classname => ["replacement_list" => ['some_string']],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }


    public function testReplacementConfHasNoReplacementName(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ReplacementConfException::class);
        $this->expectExceptionMessage("Конфигурация значений по умолчанию для класса с наименованием $classname в строке 1 не содержит ключа replacement_name");

        $conf_example = 
        [
            $classname => ["replacement_list" => [["replacement_value" => "some"]]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }

    public function testReplacementConfHasNotReplacementValue(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ReplacementConfException::class);
        $this->expectExceptionMessage("Конфигурация значений по умолчанию для класса с наименованием $classname в строке 1 не содержит ключа replacement_value");

        $conf_example = 
        [
            $classname => ["replacement_list" => [["replacement_name" => "some"]]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }

    public function testReplacementConfReplacementNameIsNotString(): void
    {
        $classname = "Somenamespace\SimpleClass";
        $this->expectException(ReplacementConfException::class);
        $this->expectExceptionMessage("Конфигурация значений по умолчанию для класса с наименованием $classname в строке 1 содержит нестроковый тип для значения по ключу replacement_name");

        $conf_example = 
        [
            $classname = ["replacement_list" => [["replacement_name" => 111, "replacement_value" => "some"]]],
        ];

        $confParamsChecker = new ConfParamsChecker();
        $confParamsChecker->checkConf($conf_example);
    }


}