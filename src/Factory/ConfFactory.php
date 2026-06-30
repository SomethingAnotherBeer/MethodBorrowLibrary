<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow\Factory;

use Somethinganotherbeer\Methodborrow\Checker\ConfParamsChecker;
use Somethinganotherbeer\Methodborrow\Conf\ClassConf;
use Somethinganotherbeer\Methodborrow\Conf\ClassConfList;
use Somethinganotherbeer\Methodborrow\Conf\ImplementationConf;
use Somethinganotherbeer\Methodborrow\Conf\ImplementationConfList;
use Somethinganotherbeer\Methodborrow\Conf\ReplacementConf;
use Somethinganotherbeer\Methodborrow\Conf\ReplacementConfList;

class ConfFactory
{
    private ConfParamsChecker $confParamsChecker;


    public static function makeInstance(): ConfFactory
    {
        return new ConfFactory(new ConfParamsChecker());
    }

    public function __construct(ConfParamsChecker $confParamsChecker)
    {
        $this->confParamsChecker = $confParamsChecker;
    }

    public function makeClassConf(string $class_name, array $params): ClassConf
    {
        $implementation_conf_params_list = $params['implementation_list'] ?? [];
        $replacement_conf_params_list = $params['replacement_list'] ?? [];

        $implementationConfList = $this->makeImplementationConfList($implementation_conf_params_list);
        $replacementConfList = $this->makeReplacementConfList($replacement_conf_params_list);

        return ClassConf::makeInstance($class_name, $implementationConfList, $replacementConfList);
    }

    public function makeImplementationConf(string $name, mixed $value): ImplementationConf
    {
        return ImplementationConf::makeInstance($name, $value);
    }

    public function makeReplacementConf(string $name, mixed $value): ReplacementConf
    {
        return ReplacementConf::makeInstance($name, $value);
    }

    public function makeClassConfList(array $params_list): ClassConfList
    {   
        /** @var ClassConf[] $class_conf_list */
        $class_conf_list = [];

        foreach ($params_list as $classname => $params) {
            $class_conf_list[$classname] = $this->makeClassConf($classname, $params);
        }

        return ClassConfList::makeInstance($class_conf_list);

    }

    public function makeImplementationConfList(array $implementation_conf_params_list): ImplementationConfList
    {
        /** @var ImplementationConf[] $implementation_conf_list */
        $implementation_conf_list = [];

        foreach ($implementation_conf_params_list as $implementation_conf_params) {
            $implementation_conf_list[$implementation_conf_params['implementation_name']] = $this->makeImplementationConf($implementation_conf_params['implementation_name'], $implementation_conf_params['implementation_value']);
        }

        return ImplementationConfList::makeInstance($implementation_conf_list);

    }

    public function makeReplacementConfList(array $replacement_conf_params_list): ReplacementConfList
    {   
        /** @var ReplacementConf[] $replacement_conf_list */
        $replacement_conf_list = [];

        foreach ($replacement_conf_params_list as $replacement_conf_params) {
            $replacement_conf_list[$replacement_conf_params['replacement_name']] = $this->makeReplacementConf($replacement_conf_params['replacement_name'], $replacement_conf_params['replacement_value']);
        }

        return ReplacementConfList::makeInstance($replacement_conf_list);
    }

}