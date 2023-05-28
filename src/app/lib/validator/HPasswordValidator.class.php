<?php

use Adianti\Validator\TFieldValidator;
use App\Helpers\PasswordHelper;

/**
 * Date validation
 *
 * @version    1.0
 * @package    validator
 * @author     Henrique Andrade
 */
class HPasswordValidator extends TFieldValidator
{
    const MIN_LENGTH = 8;
    const MAX_LENGTH = 20;
    /**
     * Validate a given value
     * @param $label Identifies the value to be validated in case of exception
     * @param $value Value to be validated
     * @param $parameters aditional parameters for validation (ex: mask)
     */
    public function validate($label, $value, $parameters = NULL)
    {
        $length = strlen($value);

        if ($length < self::MIN_LENGTH) {
            throw new Exception(PasswordHelper::helpTip());
        }

        if ($length > self::MAX_LENGTH) {
            throw new Exception(PasswordHelper::helpTip());
        }

        if (!preg_match("/\d/", $value)) {
            throw new Exception(PasswordHelper::helpTip());
        }

        if (!preg_match("/[A-Z]/", $value)) {
            throw new Exception(PasswordHelper::helpTip());
        }

        if (!preg_match("/[a-z]/", $value)) {
            throw new Exception(PasswordHelper::helpTip());
        }

        if (!preg_match("/[^a-zA-Z\d\s]/", $value)) {
            throw new Exception(PasswordHelper::helpTip());
        }
    }
}
