<?php

namespace App\Rules;

use App\PasswordPolicy;
use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $this->isStrong($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must include an uppercase letter, a lowercase letter, a number, and a special character.';
    }

    public static function isStrong($password): bool
    {
        $minLength = (int)PasswordPolicy::where('status', 1)->first()->min_length;
        if (strlen($password) >= $minLength &&
            preg_match('/[A-Z]/', $password) && // Uppercase letter
            preg_match('/[a-z]/', $password) && // Lowercase letter
            preg_match('/[0-9]/', $password) && // Number
            preg_match('/[\W_]/', $password)) { // Special character (non-alphanumeric)
            return true;
        }

        return false;
    }
}
