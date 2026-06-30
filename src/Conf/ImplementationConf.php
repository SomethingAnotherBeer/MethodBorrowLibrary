<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow\Conf;

class ImplementationConf
{
    private string $name;
    private mixed $value;

    public static function makeInstance(string $name, mixed $value): ImplementationConf
    {
        return new ImplementationConf($name, $value);
    }


    public function __construct(string $name, mixed $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

}