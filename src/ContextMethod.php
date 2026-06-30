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


    public function __invoke(mixed ...$args): mixed
    {

       if (count($this->binded_args) > 0) {
            $args = array_merge($this->binded_args, [...$args]);
       }

       return $this->rMethod->invoke($this->contextObject, ...$args);
    }

    public function bind(mixed $value): static
    {
        $this->binded_args[] = $value;

        return $this;
    }

    public function bindArgs(array $args): static
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