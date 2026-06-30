<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow\Conf;


class ClassConfList
{   
    /** @var ClassConf[] $class_conf_list */
    private array $class_conf_list;

    /** 
     * @param ClassConf[] $class_conf_list
     * @return ClassConfList
     */
    public static function makeInstance(array $class_conf_list): ClassConfList
    {
        return new ClassConfList($class_conf_list);
    }


    /**
     * @param ClassConf[] $class_conf_list
     */
    public function __construct(array $class_conf_list)
    {
        $this->class_conf_list = $class_conf_list;
    }

    /**
     * @return ClassConf[]
     */
    public function all(): array
    {
        return $this->class_conf_list;
    }

    public function count(): int
    {
        return count($this->class_conf_list);
    }

    public function get(string $name): ?ClassConf
    {
        return array_key_exists($name, $this->class_conf_list) ? $this->class_conf_list[$name] : null;        
    }

}