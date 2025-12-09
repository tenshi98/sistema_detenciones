<?php
/*
* ===================================================================
* Validator Utility
*
* Utilidades para validación y sanitización de datos
*/

namespace App\Utils;

class Validator {
    /*
    * ===================================================================
    * Valida que un campo no esté vacío
    *
    * @param mixed $value
    * @return bool
    */
    public static function required($value): bool {
        if (is_null($value)) {
            return false;
        }
        if (is_string($value) && trim($value) === '') {
            return false;
        }
        return true;
    }

    /*
    * ===================================================================
    * Valida que un valor sea un email válido
    *
    * @param string $email
    * @return bool
    */
    public static function email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /*
    * ===================================================================
    * Valida que un valor sea numérico
    *
    * @param mixed $value
    * @return bool
    */
    public static function numeric($value): bool {
        return is_numeric($value);
    }

    /*
    * ===================================================================
    * Valida que un valor sea un entero
    *
    * @param mixed $value
    * @return bool
    */
    public static function integer($value): bool {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /*
    * ===================================================================
    * Valida longitud mínima
    *
    * @param string $value
    * @param int $min
    * @return bool
    */
    public static function minLength(string $value, int $min): bool {
        return mb_strlen($value) >= $min;
    }

    /*
    * ===================================================================
    * Valida longitud máxima
    *
    * @param string $value
    * @param int $max
    * @return bool
    */
    public static function maxLength(string $value, int $max): bool {
        return mb_strlen($value) <= $max;
    }

    /*
    * ===================================================================
    * Valida que un valor esté en un array de opciones
    *
    * @param mixed $value
    * @param array $options
    * @return bool
    */
    public static function in($value, array $options): bool {
        return in_array($value, $options, true);
    }

    /*
    * ===================================================================
    * Valida formato de fecha
    *
    * @param string $date
    * @param string $format
    * @return bool
    */
    public static function date(string $date, string $format = 'Y-m-d'): bool {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /*
    * ===================================================================
    * Valida formato de hora
    *
    * @param string $time
    * @return bool
    */
    public static function time(string $time): bool {
        return preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $time) === 1;
    }

    /*
    * ===================================================================
    * Sanitiza una cadena de texto
    *
    * @param string $value
    * @return string
    */
    public static function sanitizeString(string $value): string {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    /*
    * ===================================================================
    * Sanitiza un email
    *
    * @param string $email
    * @return string
    */
    public static function sanitizeEmail(string $email): string {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /*
    * ===================================================================
    * Sanitiza un entero
    *
    * @param mixed $value
    * @return int
    */
    public static function sanitizeInt($value): int {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /*
    * ===================================================================
    * Valida múltiples reglas
    *
    * @param array $data Datos a validar
    * @param array $rules Reglas de validación
    * @return array Array con errores (vacío si no hay errores)
    */
    public static function validate(array $data, array $rules): array {
        $errors = [];

        foreach ($rules as $field => $fieldRules) {
            $value       = $data[$field] ?? null;
            $fieldErrors = [];

            foreach ($fieldRules as $rule => $param) {
                // Si la regla no tiene parámetros
               if (is_numeric($rule)) {
                    $rule  = $param;
                    $param = null;
                }

                switch ($rule) {
                    case 'required': if (!self::required($value)) {                    $fieldErrors[] = "El campo {$field} es requerido"; } break;
                    case 'email':    if ($value && !self::email($value)) {             $fieldErrors[] = "El campo {$field} debe ser un email válido"; } break;
                    case 'numeric':  if ($value && !self::numeric($value)) {           $fieldErrors[] = "El campo {$field} debe ser numérico"; } break;
                    case 'integer':  if ($value && !self::integer($value)) {           $fieldErrors[] = "El campo {$field} debe ser un entero"; } break;
                    case 'min':      if ($value && !self::minLength($value, $param)) { $fieldErrors[] = "El campo {$field} debe tener al menos {$param} caracteres"; } break;
                    case 'max':      if ($value && !self::maxLength($value, $param)) { $fieldErrors[] = "El campo {$field} debe tener máximo {$param} caracteres"; } break;
                    case 'in':       if ($value && !self::in($value, $param)) {        $fieldErrors[] = "El campo {$field} debe ser uno de: " . implode(', ', $param); } break;
                    case 'date':     if ($value && !self::date($value)) {              $fieldErrors[] = "El campo {$field} debe ser una fecha válida (Y-m-d)"; } break;
                    case 'time':     if ($value && !self::time($value)) {              $fieldErrors[] = "El campo {$field} debe ser una hora válida (HH:MM:SS)"; } break;
                }
            }

            if (!empty($fieldErrors)) {
                $errors[$field] = $fieldErrors;
            }
        }

        return $errors;
    }
}
