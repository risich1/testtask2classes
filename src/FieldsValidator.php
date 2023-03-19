<?php

namespace App;

class FieldsValidator {

    public static function validate(mixed $value, string $rules): bool {
        $results = [];

        foreach (explode('|', $rules) as $rule) {
            preg_match('/\{(.+?)\}/', $rule, $allowedValues);

            $rulesActions = [
                'only_chars' => fn($v) => static::validateOnlyChars($v),
                'int' => fn($v) => static::validateNumber($v),
                'sql_date' => fn($v) => static::validateSqlDate($v),
                'string' => fn($v) => static::validateString($v)
            ];

            foreach ($rulesActions as $actionName => $action) {
                if (str_contains($rule, $actionName)) {
                    $results[] = $action($value) && (!$allowedValues || in_array($value, explode(',', $allowedValues[1])));
                    break;
                }
            }
        }

        return (bool) array_filter($results, fn($res) => $res);
    }

    public static function validateSqlDate(mixed $value): bool {
        $date = explode('-', $value);
        return count($date) === 3 && checkdate($date[1], $date[2], $date[0]);
    }

    public static function validateNumber(mixed $value): bool {
        return is_int($value);
    }

    public static function validateOnlyChars(mixed $value): bool {
        return ctype_alpha((string) $value);
    }

    public static function validateString(mixed $value): bool {
        return is_string($value);
    }

}
