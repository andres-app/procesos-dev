<?php
//Controlador/AuthAdmin.php

class AuthAdmin
{
    public static function generateBase32Secret(int $length = 32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, 31)];
        }

        return $secret;
    }

    public static function getOtpAuthUri(string $issuer, string $accountName, string $secret): string
    {
        $label = rawurlencode($issuer . ':' . $accountName);
        $issuerEncoded = rawurlencode($issuer);

        return "otpauth://totp/{$label}?secret={$secret}&issuer={$issuerEncoded}&algorithm=SHA1&digits=6&period=30";
    }

    public static function verifyTotp(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', $code);

        if (!preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $timeSlice = (int) floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $calculated = self::getTotpCode($secret, $timeSlice + $i);
            if (hash_equals($calculated, $code)) {
                return true;
            }
        }

        return false;
    }

    private static function getTotpCode(string $secret, int $timeSlice): string
    {
        $secretKey = self::base32Decode($secret);

        if ($secretKey === '') {
            return '';
        }

        $time = pack('N*', 0) . pack('N*', $timeSlice);
        $hm = hash_hmac('sha1', $time, $secretKey, true);

        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashPart = substr($hm, $offset, 4);

        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;
        $modulo = 10 ** 6;

        return str_pad((string) ($value % $modulo), 6, '0', STR_PAD_LEFT);
    }

    private static function base32Decode(string $secret): string
    {
        $map = array_flip(str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567'));
        $secret = strtoupper($secret);
        $secret = preg_replace('/[^A-Z2-7]/', '', $secret);

        $buffer = 0;
        $bitsLeft = 0;
        $result = '';

        $chars = str_split($secret);
        foreach ($chars as $char) {
            if (!isset($map[$char])) {
                return '';
            }

            $buffer = ($buffer << 5) | $map[$char];
            $bitsLeft += 5;

            while ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $result .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $result;
    }
}