<?php

namespace Litstack\TwoFA;

interface TwoFA
{
    /**
     * Generate secret key.
     *
     * @param  int    $length
     * @param  string $prefix
     * @return string
     */
    public function generateSecretKey($length = 16, $prefix = '');

    /**
     * Verify key.
     *
     * @param  string $secretKey
     * @param  string $secret
     * @return bool
     */
    public function verifyKey($secretKey, $secret): bool;

    /**
     * Creates a QR code url.
     *
     * @param  string $company
     * @param  string $holder
     * @param  string $secret
     * @return string
     */
    public function getQRCodeUrl($company, $holder, $secret);
}
