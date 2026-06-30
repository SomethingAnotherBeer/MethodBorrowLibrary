<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Tests\ClassData;

class ClassThree implements DataInterface
{
    public function getNumber(): int
    {
        return 10;
    }
}