<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Tests\ClassData;

class ClassTwo
{
    private ClassFive $classFive;

    public function __construct(ClassFive $classFive)
    {
        $this->classFive = $classFive;
    }

    public function getSomeNumber(): int
    {
        return $this->classFive->getSomeNumber();
    }


}