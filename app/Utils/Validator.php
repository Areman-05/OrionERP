<?php

namespace OrionERP\Utils;

class Validator
{
    public static function email(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required($value): bool
    {
        return !empty($value);
    }

    public static function minLength(string $value, int $min): bool
    {
        return strlen($value) >= $min;
    }

    public static function maxLength(string $value, int $max): bool
    {
        return strlen($value) <= $max;
    }

    public static function numeric($value): bool
    {
        return is_numeric($value);
    }

    public static function integer($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    public static function decimal($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }

    public static function date(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public static function url(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function phone(string $phone): bool
    {
        return preg_match('/^[0-9\s\-\+\(\)]+$/', $phone) === 1;
    }

    public static function dni(string $dni): bool
    {
        return preg_match('/^[0-9]{8}[A-Z]$/', strtoupper($dni)) === 1;
    }

    public static function cif(string $cif): bool
    {
        return preg_match('/^[A-Z][0-9]{8}$/', strtoupper($cif)) === 1;
    }

    public static function validate(array $data, array $rules): array
    {
        $errors = [];
        
        foreach ($rules as $field => $fieldRules) {
            $value = $data[$field] ?? null;
            $fieldRulesArray = is_array($fieldRules) ? $fieldRules : explode('|', $fieldRules);
            
            foreach ($fieldRulesArray as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;
                
                switch ($ruleName) {
                    case 'required':
                        if (!self::required($value)) {
                            $errors[$field][] = "El campo $field es requerido";
                        }
                        break;
                    case 'email':
                        if ($value && !self::email($value)) {
                            $errors[$field][] = "El campo $field debe ser un email válido";
                        }
                        break;
                    case 'min':
                        if ($value && !self::minLength($value, (int)$ruleValue)) {
                            $errors[$field][] = "El campo $field debe tener al menos $ruleValue caracteres";
                        }
                        break;
                    case 'max':
                        if ($value && !self::maxLength($value, (int)$ruleValue)) {
                            $errors[$field][] = "El campo $field no puede tener más de $ruleValue caracteres";
                        }
                        break;
                    case 'numeric':
                        if ($value && !self::numeric($value)) {
                            $errors[$field][] = "El campo $field debe ser numérico";
                        }
                        break;
                    case 'integer':
                        if ($value && !self::integer($value)) {
                            $errors[$field][] = "El campo $field debe ser un número entero";
                        }
                        break;
                    case 'decimal':
                        if ($value && !self::decimal($value)) {
                            $errors[$field][] = "El campo $field debe ser un número decimal";
                        }
                        break;
                    case 'date':
                        if ($value && !self::date($value, $ruleValue ?? 'Y-m-d')) {
                            $errors[$field][] = "El campo $field debe ser una fecha válida";
                        }
                        break;
                }
            }
        }
        
        return $errors;
    }

    public static function sanitizeString(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = self::sanitizeString($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }

    public static function validarIBAN(string $iban): bool
    {
        $iban = str_replace(' ', '', strtoupper($iban));
        if (strlen($iban) < 15 || strlen($iban) > 34) {
            return false;
        }
        
        $iban = substr($iban, 4) . substr($iban, 0, 4);
        $iban = preg_replace_callback('/[A-Z]/', function($matches) {
            return ord($matches[0]) - ord('A') + 10;
        }, $iban);
        
        return bcmod($iban, '97') == 1;
    }

    public static function validarNIF(string $nif): bool
    {
        $nif = strtoupper(trim($nif));
        if (strlen($nif) != 9) {
            return false;
        }
        
        $letras = 'TRWAGMYFPDXBNJZSQVHLCKE';
        $numero = substr($nif, 0, 8);
        $letra = substr($nif, 8, 1);
        
        if (!is_numeric($numero)) {
            return false;
        }
        
        return $letras[$numero % 23] === $letra;
    }
}

