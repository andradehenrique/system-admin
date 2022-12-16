<?php

namespace App\Helpers;

class HashHelper
{
    public function __construct(private string $algorithm = PASSWORD_ARGON2I)
    {
    }

    public function hash($string): string
    {
        return password_hash($string, $this->algorithm);
    }

    public function verify($string, $hash): bool
    {
        return password_verify($string, $hash);
    }
}