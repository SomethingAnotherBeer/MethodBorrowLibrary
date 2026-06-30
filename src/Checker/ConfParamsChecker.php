<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow\Checker;

use Somethinganotherbeer\Methodborrow\Exception\ConfigurationException;

class ConfParamsChecker
{
    public function checkConf(array $conf_params_list): void
    {
        $int_keys = array_filter($conf_params_list, fn(string|int $key) => is_int($key), ARRAY_FILTER_USE_KEY);
        if (count($int_keys) > 0) {
            $int_keys_msg = implode("|", $int_keys);
            throw new ConfigurationException("Недопустимый формат конфигурации, следующие ключи конфигурации являются числовыми: $int_keys_msg");
        }

        $conf_params_classname_list = array_keys($conf_params_list);

        $bad_classname_params = [];
        $current_position = 1;

        foreach ($conf_params_classname_list as $classname) {
            $current_check = [];
            $base_msg = "В строке № {$current_position} ";
            if ($this->checkClassNameStartsWithDigit($classname)) {
                $current_check[] = "$base_msg наименование класса начинается с цифры";
            }
            if ($this->checkClassNameHasDash($classname)) {
                $current_check[] = "$base_msg наименование класса содержит тире";
            }

            if (count($current_check) > 0) {
                $bad_classname_params[] = implode("\n", $current_check);
            }
            $current_position+=1;
        }

        if (count($bad_classname_params) > 0) {
            $bad_classname_params_str = implode("\n", $bad_classname_params);
            throw new ConfigurationException($bad_classname_params_str);
        }

        $unexpected_type_conf_params = array_filter($conf_params_list, fn(mixed $value) => !is_array($value));
        if (count($unexpected_type_conf_params) > 0) {
            $unexpected_type_conf_params = implode("\n", array_keys($unexpected_type_conf_params));
            throw new ConfigurationException("Следующие ключи с наименованиями классов содержат отличный от массива тип значения: $unexpected_type_conf_params");
        }

        if (count(array_unique($conf_params_classname_list)) < count($conf_params_classname_list)) {
            throw new ConfigurationException("Ключи конфигурации должны содержать только уникальные наименования имен классов");
        }

        $errors_list = [];

        foreach ($conf_params_list as $classname => $conf_params) {
            if (array_key_exists('implementation_list', $conf_params)) {
                $errors = $this->checkImplementationConfList($classname, $conf_params['implementation_list']);
                if (count($errors) > 0) {
                    $errors_list[] = implode("|", $errors);
                }
            }

            if (array_key_exists('relpacement_list', $conf_params)) {
                $errors = $this->checkReplacementConfList($classname, $conf_params['replacement_list']);
                if (count($errors) > 0) {
                    $errors_list = implode("|", $errors);
                }

            }
        }

        if (count($errors_list) > 0) {
            $errors_list_str = implode("\n", $errors_list);
            throw new ConfigurationException($errors_list_str);
        }




    }

    private function checkImplementationConfList(string $classname, mixed $implementation_conf_list): array
    {
        $errors = [];
        $is_stop_write_errors = false;

        if (!is_array($implementation_conf_list)) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname не является массивом";
            $is_stop_write_errors = true;
        }



        if (!array_key_exists('implementation_name', $implementation_conf_list) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname не содержит ключа implementation_name";
        }

        if (!array_key_exists('implementation_value', $implementation_conf_list) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname не содержит ключа implementation_value";
        }

        if (!is_string($implementation_conf_list['implementation_name']) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname содержит нестроковый тип для значения по ключу implementation_name";
        }

        if (!is_string($implementation_conf_list['implementation_value']) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname содержит нестроковый тип для значения по ключу implementation_value";
        }

        if ($this->checkClassNameStartsWithDigit($implementation_conf_list['implementation_name']) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname по ключу implementation_name содержит наименование класса, которое начинается с цифры";
        }

        if ($this->checkClassNameHasDash($implementation_conf_list['implementation_name']) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname по ключу implementation_name содержит наименование класса, которое содержит тире";
        }

        if ($this->checkClassNameStartsWithDigit($implementation_conf_list['implementation_value']) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname по ключу implementation value содержит наименование класса, которое начинается с цифры";
        }

        if ($this->checkClassNameHasDash($implementation_conf_list['implementation_value']) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname по ключу implementation_value содержит наименование класса, которое содержит тире";
        }

        return $errors;

    }

    private function checkReplacementConfList(string $classname, mixed $replacement_conf_list): array
    {
        $errors = [];
        $is_stop_write_errors = false;

        if (!is_array($replacement_conf_list)) {
            $errors[] = "Конфигурация значений по умолчанию для класса с наименованием $classname не является массивом";
            $is_stop_write_errors = true;
        }

        if (!array_key_exists('replacement_name', $replacement_conf_list) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация значений по умолчанию для класса с наименованием $classname не содержит ключа replacement_name";
        }

        if (!array_key_exists('replacement_value', $replacement_conf_list) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация значений по умолчанию для класса с наименованием $classname не содержит ключа replacement_value";
        }

        if (!is_string($replacement_conf_list['replacement_name']) && !$is_stop_write_errors) {
            $errors[] = "Конфигурация значений по умолчанию для класса с наименованием $classname содержит нестроковый тип для значения по ключу replacement_name";
        }

        return $errors;
        
    }

    private function checkClassNameStartsWithDigit(string $class_name): bool
    {
        return (preg_match("/^\d/", $class_name)) ? true : false;
    }

    private function checkClassNameHasDash(string $class_name): bool
    {   
        return (preg_match("/-/", $class_name)) ? true : false;
    }




}