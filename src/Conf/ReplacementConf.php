<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow\Conf;

class ReplacementConf
{
    private string $name;
    private mixed $value;

    public static function makeInstance(string $name, mixed $value): ReplacementConf
    {
        return new ReplacementConf($name, $value);
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