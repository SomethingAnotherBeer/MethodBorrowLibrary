<?php
declare(strict_types=1);
namespace Somethinganotherbeer\Methodborrow\Checker;

use Somethinganotherbeer\Methodborrow\Exception\ClassNameHasDashException;
use Somethinganotherbeer\Methodborrow\Exception\ClassNameStartsWithDigitException;
use Somethinganotherbeer\Methodborrow\Exception\ConfClassKeyNotReferenceOnArrayException;
use Somethinganotherbeer\Methodborrow\Exception\ConfClassKeysIsNumericException;
use Somethinganotherbeer\Methodborrow\Exception\ConfigurationException;
use Somethinganotherbeer\Methodborrow\Exception\ImplementationConfException;
use Somethinganotherbeer\Methodborrow\Exception\ReplacementConfException;

class ConfParamsChecker
{
    public function checkConf(array $conf_params_list): void
    {
        $int_keys = array_filter($conf_params_list, fn(string|int $key) => is_int($key), ARRAY_FILTER_USE_KEY);
        if (count($int_keys) > 0) {
            $int_keys_msg = implode("|", $int_keys);
            throw new ConfClassKeysIsNumericException("Недопустимый формат конфигурации, следующие ключи конфигурации являются числовыми: $int_keys_msg");
        }

        $conf_params_classname_list = array_keys($conf_params_list);

        $classname_starts_with_digit_list = [];
        $classname_with_dash_list = [];

        $current_position = 1;

        foreach ($conf_params_classname_list as $classname) {
            $base_msg = "В строке № {$current_position} ";
            if ($this->checkClassNameStartsWithDigit($classname)) {
                $classname_starts_with_digit_list[] = "$base_msg наименование класса {$classname} начинается с цифры";
            }
            if ($this->checkClassNameHasDash($classname)) {
                $classname_with_dash_list[] = "$base_msg наименование класса {$classname} содержит тире";
            }

            $current_position+=1;
        }

        if (count($classname_starts_with_digit_list) > 0) {
            $classname_starts_with_digit_list_str = implode("\n", $classname_starts_with_digit_list);
            throw new ClassNameStartsWithDigitException($classname_starts_with_digit_list_str);
        }

        if (count($classname_with_dash_list) > 0) {
            $classname_with_dash_list_str = implode("\n", $classname_with_dash_list);
            throw new ClassNameHasDashException($classname_with_dash_list_str);
        }

        $unexpected_type_conf_params = array_filter($conf_params_list, fn(mixed $value) => !is_array($value));
        if (count($unexpected_type_conf_params) > 0) {
            $unexpected_type_conf_params = implode("\n", array_keys($unexpected_type_conf_params));
            throw new ConfClassKeyNotReferenceOnArrayException("Следующие ключи с наименованиями классов содержат отличный от массива тип значения: $unexpected_type_conf_params");
        }

        $implementation_conf_errors = [];
        $replacement_conf_errors = [];

        foreach ($conf_params_list as $classname => $conf_params) {
            if (array_key_exists('implementation_list', $conf_params)) {
                if (!is_array($conf_params['implementation_list'])) {
                    throw new ImplementationConfException("Значение implementation_list для класса с наименованием $classname не является массивом");
                }
                $current_row = 1;
                foreach ($conf_params['implementation_list'] as $implementation_item) {
                    $errors = $this->checkImplementationConfList($classname, $implementation_item, $current_row);
                    if (count($errors) > 0) {
                        $implementation_conf_errors[] = implode("\n", $errors);
                    }
                    $current_row++;
                }

                if (count($implementation_conf_errors) > 0) {
                    $implementation_conf_errors_str = implode("\n", $implementation_conf_errors);
                    throw new ImplementationConfException($implementation_conf_errors_str);
                }
            }

            if (array_key_exists('replacement_list', $conf_params)) {
                if (!is_array($conf_params['replacement_list'])) {
                    throw new ReplacementConfException("Значение replacement_list для класса с наименованием $classname не является массивом");
                }
                $current_row = 1;
                foreach ($conf_params['replacement_list'] as $replacement_item) {
                    $errors = $this->checkReplacementConfList($classname, $replacement_item, $current_row);
                    if (count($errors) > 0) {
                        $replacement_conf_errors[] = implode("\n", $errors);
                    }
                    $current_row++;
                }

                if (count($replacement_conf_errors) > 0) {
                    $replacement_conf_errors_str = implode("\n", $replacement_conf_errors);
                    throw new ReplacementConfException($replacement_conf_errors_str);
                }

            }
        }

    }

    private function checkImplementationConfList(string $classname, mixed $implementation_conf_list, int $current_row): array
    {
        $errors = [];
        $is_stop_write_errors = false;

        if (!is_array($implementation_conf_list)) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname в строке $current_row не является массивом";
            $is_stop_write_errors = true;
        }

        if (!$is_stop_write_errors && !array_key_exists('implementation_name', $implementation_conf_list)) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname в строке $current_row не содержит ключа implementation_name";
            $is_stop_write_errors = true;
        }

        if (!$is_stop_write_errors && !array_key_exists('implementation_value', $implementation_conf_list)) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname в строке $current_row не содержит ключа implementation_value";
            $is_stop_write_errors = true;
        }

        if (!$is_stop_write_errors && !is_string($implementation_conf_list['implementation_name'])) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname в строке $current_row содержит нестроковый тип для значения по ключу implementation_name";
            $is_stop_write_errors = true;
        }

        if (!$is_stop_write_errors && !is_string($implementation_conf_list['implementation_value'])) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname в строке $current_row содержит нестроковый тип для значения по ключу implementation_value";
            $is_stop_write_errors = true;
        }

        if (!$is_stop_write_errors && $this->checkClassNameStartsWithDigit($implementation_conf_list['implementation_name'])) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname в строке $current_row по ключу implementation_name содержит наименование класса, которое начинается с цифры";
        }

        if (!$is_stop_write_errors && $this->checkClassNameHasDash($implementation_conf_list['implementation_name'])) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname в строке $current_row по ключу implementation_name содержит наименование класса, которое содержит тире";
        }

        if (!$is_stop_write_errors && $this->checkClassNameStartsWithDigit($implementation_conf_list['implementation_value'])) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname в строке $current_row по ключу implementation value содержит наименование класса, которое начинается с цифры";
        }

        if (!$is_stop_write_errors && $this->checkClassNameHasDash($implementation_conf_list['implementation_value'])) {
            $errors[] = "Конфигурация реализации для класса с наименованием $classname в строке $current_row по ключу implementation_value содержит наименование класса, которое содержит тире";
        }

        return $errors;

    }

    private function checkReplacementConfList(string $classname, mixed $replacement_conf_list, int $current_row): array
    {
        $errors = [];
        $is_stop_write_errors = false;

        if (!is_array($replacement_conf_list)) {
            $errors[] = "Конфигурация значений по умолчанию для класса с наименованием $classname в строке $current_row не является массивом";
            $is_stop_write_errors = true;
        }

        if (!$is_stop_write_errors && !array_key_exists('replacement_name', $replacement_conf_list)) {
            $errors[] = "Конфигурация значений по умолчанию для класса с наименованием $classname в строке $current_row не содержит ключа replacement_name";
            $is_stop_write_errors = true;
        }

        if (!$is_stop_write_errors && !array_key_exists('replacement_value', $replacement_conf_list)) {
            $errors[] = "Конфигурация значений по умолчанию для класса с наименованием $classname в строке $current_row не содержит ключа replacement_value";
            $is_stop_write_errors = true;
        }

        if (!$is_stop_write_errors && !is_string($replacement_conf_list['replacement_name'])) {
            $errors[] = "Конфигурация значений по умолчанию для класса с наименованием $classname в строке $current_row содержит нестроковый тип для значения по ключу replacement_name";
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