<?php

namespace App\Support;

use Illuminate\Support\Facades\Crypt;

/**
 * Wrapper for encrypting/decrypting sensitive fields (e.g. registration numbers).
 * Uses Laravel's built-in AES-256-CBC encryption tied to APP_KEY.
 */
final class Encryption
{
    public static function encrypt(string $value): string
    {
        return Crypt::encryptString($value);
    }

    public static function decrypt(string $encrypted): string
    {
        return Crypt::decryptString($encrypted);
    }
}
