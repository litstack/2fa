<?php

namespace Litstack\TwoFA;

use PragmaRX\Google2FAQRCode\Google2FA as G2FA;

class Google2FA implements TwoFa
{
    protected $google2fa;

    public function __construct()
    {
        $this->google2fa = new G2FA;
    }

    /**
     * Generate secret key.
     *
     * @param  int    $length
     * @param  string $prefix
     * @return string
     */
    public function generateSecretKey($length = 16, $prefix = '')
    {
        return $this->google2fa->generateSecretKey($length, $prefix);
    }

    /**
     * Verify key.
     *
     * @param  string $secretKey
     * @param  string $secret
     * @return bool
     */
    public function verifyKey($secretKey, $secret): bool
    {
        return $this->google2fa->verifyKey($secretKey, $secret);
    }

    /**
     * Creates a QR code url.
     *
     * @param  string $company
     * @param  string $holder
     * @param  string $secret
     * @return string
     */
    public function getQRCodeUrl($company, $holder, $secret)
    {
        return $this->google2fa->getQRCodeInline($company, $holder, $secret);
    }
}
