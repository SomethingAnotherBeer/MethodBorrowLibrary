<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow;

use ReflectionMethod;

class ContextMethod
{
    private Object $contextObject;
    private ReflectionMethod $rMethod;

    private array $binded_args = [];

    public static function makeInstance(Object $contextObject, ReflectionMethod $rMethod): ContextMethod
    {
        return new ContextMethod($contextObject, $rMethod);
    }

    public function __construct(Object $contextObject, ReflectionMethod $rMethod)
    {
        $this->contextObject = $contextObject;
        $this->rMethod = $rMethod;
    }


    public function __invoke(array $args): mixed
    {
       return $this->rMethod->invoke($this->contextObject, ...$args);
    }

    public function bindOne(mixed $value): static
    {
        $this->binded_args[] = $value;

        return $this;
    }

    public function bind(array $args): static
    {
        foreach ($args as $arg) {
            $this->binded_args[] = $arg;
        }

        return $this;
    }

    public function clear(): static
    {
        array_splice($this->binded_args, 0, count($this->binded_args));

        return $this;
    }


}