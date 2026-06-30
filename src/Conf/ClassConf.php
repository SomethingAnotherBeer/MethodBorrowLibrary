<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow\Conf;

class ClassConf
{
    private string $name;

    private ImplementationConfList $implementationConfList;
    private ReplacementConfList $replacementConfList;

    public static function makeInstance(string $name, ImplementationConfList $implementationConfList, ReplacementConfList $replacementConfList): ClassConf
    {
        return new ClassConf($name, $implementationConfList, $replacementConfList);
    }


    public function __construct(string $name, ImplementationConfList $implementationConfList, ReplacementConfList $replacementConfList)
    {
        $this->name = $name;
        $this->implementationConfList = $implementationConfList ?? null;
        $this->replacementConfList = $replacementConfList;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImplementationConfList(): ImplementationConfList
    {
        return $this->implementationConfList;
    }

    public function getReplacementConfList(): ReplacementConfList
    {
        return $this->replacementConfList;
    }





}