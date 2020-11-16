<?php

namespace Litstack\TwoFA;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Crypt;

trait HasTwoFactorAuthentication
{
    /**
     * Get TwoFA instance.
     *
     * @return TwoFA
     */
    public function getTwoFA(): TwoFA
    {
        return app('lit.2fa');
    }

    /**
     * Get current one time password if 2 factor authentication is enabled.
     *
     * @return string|null
     */
    public function getCurrentOtp()
    {
        if (is_null($secret = $this->getTwoFASecret())) {
            return;
        }

        return $this->getTwoFa()->getCurrentOtp($secret);
    }

    /**
     * Determines if 2 factor authentication is enabled.
     *
     * @return bool
     */
    public function is2FAEnabled(): bool
    {
        $enabled = $this->getAttribute(
            $this->getTwoFAEnabledAttributeName()
        );

        if (! is_bool($enabled)) {
            return false;
        }

        return $enabled;
    }

    /**
     * Enabled 2 factor authentication.
     *
     * @return void
     */
    public function enable2FA()
    {
        if ($this->is2FAEnabled()) {
            return;
        }

        $this->setAttribute(
            $this->getTwoFAEnabledAttributeName(), true
        );
        $this->save();
    }

    /**
     * Get 2 factor authentication enabled attribute name.
     *
     * @return void
     */
    protected function getTwoFAEnabledAttributeName()
    {
        if (isset($this->two_fa_enabled_key)) {
            return $this->two_fa_enabled_key;
        }

        return 'two_fa_enabled';
    }

    /**
     * Get 2 factor authentication secret attribute name.
     *
     * @return void
     */
    protected function getTwoFASecretAttributeName()
    {
        if (isset($this->two_fa_secret_key)) {
            return $this->two_fa_secret_key;
        }

        return 'two_fa_secret';
    }

    /**
     * Disable 2 factor authentication.
     *
     * @param  string $secret
     * @return void
     */
    public function disable2FA($secret)
    {
        if (! $this->is2FAEnabled()) {
            return;
        }

        if (! $this->verifyKey($secret)) {
            throw new AuthorizationException("Incorrect one time password [{$secret}].");
        }

        $this->setAttribute(
            $this->getTwoFAEnabledAttributeName(), false
        );

        $this->setAttribute(
            $this->getTwoFASecretAttributeName(), null
        );

        $this->save();
    }

    /**
     * Generates new secret. Needs to verify key first if a secret is already set.
     *
     * @param  string $secret
     * @return string
     *
     * @throws AuthorizationException
     */
    public function generateNewSecret($secret = '')
    {
        if ($this->is2FAEnabled() && ! $this->verifyKey($secret)) {
            throw new AuthorizationException("Incorrect one time password [{$secret}].");
        }

        $secret = $this->getTwoFA()->generateSecretKey(
            $this->getTwoFASecretByteLength()
        );

        $this->setAttribute(
            $this->getTwoFASecretAttributeName(),
            Crypt::encrypt($secret)
        );

        $this->save();

        return $secret;
    }

    /**
     * Get 2 factor authentication secret byte length.
     *
     * @return int
     */
    protected function getTwoFASecretByteLength(): int
    {
        if (isset($this->two_fa_secret_byte_length)) {
            return $this->two_fa_secret_byte_length;
        }

        return 32;
    }

    /**
     * Generate qr code url.
     *
     * @return string|null
     */
    public function getQRCodeUrl()
    {
        if (is_null($secret = $this->getTwoFASecret())) {
            return;
        }

        return $this->getTwoFA()->getQRCodeUrl(
            basename(url('')), $this->email, $secret
        );
    }

    /**
     * Get decrytped two fa secret.
     *
     * @return string
     */
    public function getTwoFASecret()
    {
        $encrypted = $this->getAttribute(
            $this->getTwoFASecretAttributeName()
        );

        if (is_null($encrypted)) {
            return;
        }

        return Crypt::decrypt($encrypted);
    }

    /**
     * Verify key.
     *
     * @param  string $secretKey
     * @param  string $secret
     * @return bool
     */
    public function verifyKey($secret): bool
    {
        return $this->getTwoFA()->verifyKey($this->getTwoFASecret(), $secret);
    }
}
