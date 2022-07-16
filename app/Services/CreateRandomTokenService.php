<?php

namespace App\Services;

class CreateRandomTokenService {
    /**
     * Generate random token.
     *
     * @return string random token.
     */
    public static function generate()
    {
        $token = random_bytes(8);

        return bin2hex($token);
    }
}
