<?php

declare(strict_types=1);

namespace App\Services;

/**
 * RFC 6238 TOTP, zero third-party dependency for the core algorithm.
 * Compatible with Google Authenticator, Authy, etc. — 30-second step,
 * SHA1, 6 digits (industry-standard defaults). Ported unchanged from the
 * Core PHP build, where sign/verify round-trips and RFC compliance were
 * already verified against test vectors.
 */
class TotpService
{
    private const SECRET_LENGTH = 20; // 160-bit
    private const PERIOD = 30;
    private const DIGITS = 6;
    private const BASE32_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public function generateSecret(): string
    {
        return $this->base32Encode(random_bytes(self::SECRET_LENGTH));
    }

    public function provisioningUri(string $secret, string $accountName, string $issuer): string
    {
        $label = rawurlencode($issuer.':'.$accountName);
        $params = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => self::DIGITS,
            'period' => self::PERIOD,
        ]);

        return "otpauth://totp/{$label}?{$params}";
    }

    public function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', $code);
        if (! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $timestamp = (int) floor(time() / self::PERIOD);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals($this->generateCode($secret, $timestamp + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    public function generateCode(string $secret, ?int $timeStep = null): string
    {
        $timeStep ??= (int) floor(time() / self::PERIOD);
        $key = $this->base32Decode($secret);
        $binaryTime = str_pad($this->intToBinary($timeStep), 8, chr(0), STR_PAD_LEFT);

        $hash = hash_hmac('sha1', $binaryTime, $key, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;

        $truncated = ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);

        $code = $truncated % (10 ** self::DIGITS);

        return str_pad((string) $code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private function intToBinary(int $int): string
    {
        $result = '';
        while ($int > 0) {
            $result = chr($int & 0xFF).$result;
            $int >>= 8;
        }

        return $result;
    }

    private function base32Encode(string $data): string
    {
        $binaryString = '';
        foreach (str_split($data) as $byte) {
            $binaryString .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        }
        $output = '';
        foreach (str_split($binaryString, 5) as $chunk) {
            $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            $output .= self::BASE32_ALPHABET[bindec($chunk)];
        }

        return $output;
    }

    private function base32Decode(string $data): string
    {
        $data = strtoupper(rtrim($data, '='));
        $binaryString = '';
        foreach (str_split($data) as $char) {
            $pos = strpos(self::BASE32_ALPHABET, $char);
            if ($pos === false) {
                continue;
            }
            $binaryString .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }
        $output = '';
        foreach (str_split($binaryString, 8) as $byte) {
            if (strlen($byte) === 8) {
                $output .= chr(bindec($byte));
            }
        }

        return $output;
    }

    /** Generates one-time recovery codes shown once at 2FA setup. */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(5)));
        }

        return $codes;
    }
}
