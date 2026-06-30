<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow\Conf;

class ReplacementConfList
{   
    /** @var ReplacementConf[] $replacement_conf_list */
    private array $replacement_conf_list;

    /**
     * @param ReplacementConf[] $replacement_conf_list
     * @return ReplacementConfList
     */
    public static function makeInstance(array $replacement_conf_list): ReplacementConfList
    {
        return new ReplacementConfList($replacement_conf_list);
    }

    /**
     * @param ReplacementConf[] $replacement_conf_list
     */
    public function __construct(array $replacement_conf_list)
    {
        $this->replacement_conf_list = $replacement_conf_list;
    }

    /**
     * @return ReplacementConf[]
     */
    public function all(): array
    {
        return $this->replacement_conf_list;
    }

    public function count(): int
    {
        return count($this->replacement_conf_list);
    }


    public function get(string $name): ?ReplacementConf
    {
        return (array_key_exists($name, $this->replacement_conf_list)) ? $this->replacement_conf_list[$name]: null;
    }


}