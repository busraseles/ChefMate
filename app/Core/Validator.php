<?php

namespace App\Core;

final class Validator
{

    public static function missing(array $data, array $fields): array
    {
        $missing = [];

        foreach ($fields as $field) {
            if (trim((string)($data[$field] ?? '')) === '') {
                $missing[] = $field;
            }
        }

        return $missing;
    }

    public static function isEmail(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function minLength(string $value, int $min): bool
    {
        return mb_strlen($value) >= $min;
    }
}
