<?php

namespace App\Support;

use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

/**
 * One password policy for every account type.
 *
 * Kept in one place so admin and customer accounts can never drift apart.
 */
class PasswordPolicy
{
    /**
     * Rules for a password a person chooses themselves.
     *
     * At least 10 characters with mixed case, a number and a symbol, and checked
     * against known data breaches through the Have I Been Pwned API. Only the
     * first five characters of the password's hash ever leave the server, so
     * the password itself is never sent anywhere.
     */
    public static function rules(): Password
    {
        return Password::min(10)
            ->mixedCase()
            ->numbers()
            ->symbols()
            ->uncompromised();
    }

    /**
     * A random temporary password for a new or reset account.
     *
     * Letters and numbers only, so it is easy to copy correctly from an email.
     * At 14 characters it is still far beyond guessing range — and it only
     * works until the holder sets their own password.
     */
    public static function temporary(): string
    {
        return Str::password(14, letters: true, numbers: true, symbols: false);
    }
}
