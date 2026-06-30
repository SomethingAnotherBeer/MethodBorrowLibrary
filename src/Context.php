<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use Somethinganotherbeer\Methodborrow\Conf\ClassConfList;
use Somethinganotherbeer\Methodborrow\Exception\BuiltInParameterValueNotSpecifiedException;
use Somethinganotherbeer\Methodborrow\Exception\ClassNotFoundException;
use Somethinganotherbeer\Methodborrow\Exception\ImplementationNotImplementsSpecifiedClassException;
use Somethinganotherbeer\Methodborrow\Exception\ImplementationNotSpecifiedException;
use Somethinganotherbeer\Methodborrow\Exception\MethodNotFoundException;
class Context
{
    private ClassConfList $classConfList;

    /** @var Object[] $object_storage */
    private array $object_storage = [];


    public static function makeInstance(ClassConfList $classConfList): Context
    {
        return new Context($classConfList);
    }

    public function __construct(ClassConfList $classConfList)
    {
        $this->classConfList = $classConfList;
    }

    public function borrowMethodFromClass(string $classname, string $method_name): ContextMethod
    {

        if (!class_exists($classname)) {
            throw new ClassNotFoundException("Класс не найден");
        }
        $rClass = new ReflectionClass($classname);

        if (!$rClass->hasMethod($method_name)) {
            throw new MethodNotFoundException("Метод класса не найден");
        }

        $instance = null;

        if (array_key_exists($classname, $this->object_storage)) {
            $instance = $this->object_storage[$classname];
        }
        else {
            $instance = $this->createObject($rClass);
            $this->object_storage[$classname] = $instance;
        }

        $rMethod = new ReflectionMethod($instance, $method_name);

        return ContextMethod::makeInstance($instance, $rMethod);

    }


    private function createObject(ReflectionClass $rClass)
    {
        $args = [];

        if ($rClass->hasMethod('__construct')) {
            $rConstructor = $rClass->getConstructor();
            $classConf = $this->classConfList->get($rClass->getName());

            /** @var ReflectionParameter[] $r_parameter_list */
            $r_parameter_list = $rConstructor->getParameters();
            
            /** @var ReflectionParameter[] $built_in_parameter_without_default_list */
            $built_in_parameter_without_default_list = $this->extractAllBuiltInParametersWithoutDefault($r_parameter_list);
            
            /** @var ReflectionParameter[] $parameter_with_default_list */
            $parameter_with_default_list = $this->extractAllParametersWithDefault($r_parameter_list);
            
            /** @var ReflectionParameter[] $class_parameter_list */
            $class_parameter_list = $this->extractClassParameters($r_parameter_list);


            if (count($built_in_parameter_without_default_list) > 0 ) {
                    $unexpected_built_in_params_list = [];
                    foreach ($built_in_parameter_without_default_list as $item) {

                    if (!$classConf || !$classConf->getReplacementConfList()->get($item->getName())) {
                            $unexpected_built_in_params_list[] = $item->getName();
                    }
                    else {
                        $current = $classConf->getReplacementConfList()->get($item->getName());
                        $args[$item->getPosition()] = $current->getValue();
                    }
                }
                if (count($unexpected_built_in_params_list) > 0) {
                    $unexpected_built_in_params_list_str = implode("\n", $unexpected_built_in_params_list);
                    throw new BuiltInParameterValueNotSpecifiedException("Следующие параметры со встроенным типом не имеют дефолтных значений в конфигурации: $unexpected_built_in_params_list_str");
                }
                

            }

            if (count($parameter_with_default_list) > 0) {
                foreach ($parameter_with_default_list as $item) {
                    $args[$item->getPosition()] = $item->getDefaultValue();
                }
            }

            if (count($class_parameter_list) > 0) {
                foreach ($class_parameter_list as $item) {
                    $currentRClass = new ReflectionClass($item->getType()->getName());
                    if ($currentRClass->isAbstract() || $currentRClass->isInterface()) {
                        if (!$classConf || !$classConf->getImplementationConfList()->get($item->getType()->getName())) {
                            throw new ImplementationNotSpecifiedException("В конфигурации не указана реализация для абстрактного класса/интерфейса с именем " . $item->getType()->getName());
                        }
                        $implementation = new ReflectionClass($classConf->getImplementationConfList()->get($item->getType()->getName())->getValue());

                        if (!$implementation->isSubclassOf($currentRClass)) {
                            throw new ImplementationNotImplementsSpecifiedClassException("Указанный в конфигурации класс с именем ". $implementation->getName(). " не реализует абстрактный класс/интерфейс с именем ". $currentRClass->getName());
                        }
                        $currentRClass = $implementation;
                    }
                    
                    $args[$item->getPosition()] = $this->createObject($currentRClass);
                }
            }

            $sorted_args = [];
            for ($i = 0; $i < count($args); $i++) {
                $sorted_args[$i] = $args[$i];
            }

            return $rClass->newInstance(...$sorted_args);


        }
        else {
            return $rClass->newInstance();
        }

    }

    /**
     * @param ReflectionParameter[] $r_parameter_list
     * @return ReflectionParameter[]
     */
    private function extractAllBuiltInParametersWithoutDefault(array $r_parameter_list): array
    {   
        /** @var ReflectionParameter[] $extract_list */
        $extract_list = [];

        foreach ($r_parameter_list as $rParameter) {
            if ((!$rParameter->hasType() || $rParameter->getType()->isBuiltin()) && !$rParameter->isDefaultValueAvailable()) {
                $extract_list[] = $rParameter;
            }
        }

        return $extract_list;
    }

    /**
     * @param ReflectionParameter[] $r_parameter_list
     * @return ReflectionParameter[]
     */
    private function extractAllParametersWithDefault(array $r_parameter_list): array
    {
        return array_filter($r_parameter_list, fn(ReflectionParameter $rParameter) => $rParameter->isDefaultValueAvailable());
    }

    /**
     * @param ReflectionParameter[] $r_parameter_list
     * @return ReflectionParameter[]
     * 
     */
    private function extractClassParameters(array $r_parameter_list): array
    {
        return array_filter($r_parameter_list, fn(ReflectionParameter $rParameter) => !$rParameter->getType()->isBuiltin() && !$rParameter->isDefaultValueAvailable());
    }

}