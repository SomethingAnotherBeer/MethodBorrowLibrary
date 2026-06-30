<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow\Conf;

class ImplementationConfList
{   
    /** @var ImplementationConf[] $implementation_conf_list */
    private array $implementation_conf_list;


    /**
     * @param ImplementationConf[] $implementation_conf_list
     * @return ImplementationConfList
     */
    public static function makeInstance(array $implementation_conf_list): ImplementationConfList
    {
        return new ImplementationConfList($implementation_conf_list);
    }

    /**
     * @param ImplementationConf[] $implementation_conf_list
     */
    public function __construct(array $implementation_conf_list)
    {
        $this->implementation_conf_list = $implementation_conf_list;
    }
    
    /**
     * @return ImplementationConf[]
     */
    public function all(): array
    {
        return $this->implementation_conf_list;
    }

    public function count(): int
    {
        return count($this->implementation_conf_list);
    }

    public function get(string $name): ?ImplementationConf
    {
        return (array_key_exists($name, $this->implementation_conf_list)) ? $this->implementation_conf_list[$name] : null;
    }

}