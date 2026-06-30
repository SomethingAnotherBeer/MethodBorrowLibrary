<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Tests\ClassData;

class ClassOne
{
    private ClassTwo $classTwo;
    private DataInterface $dataInterface;
    private int $built_in_value;
    private int $for_default_value;

    public function __construct(ClassTwo $classTwo, DataInterface $dataInterface, int $built_in_value, int $for_default_value = 5)
    {
        $this->classTwo = $classTwo;
        $this->dataInterface = $dataInterface;
        $this->built_in_value = $built_in_value;
        $this->for_default_value = $for_default_value;
    }


    public function doSomething(int $summable): int
    {
        return $this->dataInterface->getNumber() * ($this->built_in_value + $this->for_default_value + $this->classTwo->getSomeNumber() + $summable);
    }



}