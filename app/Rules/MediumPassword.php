<?php

namespace App\Rules;

use App\PasswordPolicy;
use Illuminate\Contracts\Validation\Rule;

class MediumPassword implements Rule
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
        //
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must include at least one letter and one number.';
    }

    public static function isMedium($password): bool
    {
        $minLength = (int)PasswordPolicy::where('status', 1)->first()->min_length;
        if (strlen($password) >= $minLength &&
            preg_match('/[a-zA-Z]/', $password) &&  // At least one letter (lowercase or uppercase)
            preg_match('/[0-9]/', $password)) {  // At least one number
            return true;
        }

        return false;
    }
}
