<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Tests\ClassData;

class ClassX
{
    private YInterface $yClass;

    public function __construct(YInterface $yClass)
    {
        $this->yClass = $yClass;
    }


    public function simpleDo(): void
    {

    }

}