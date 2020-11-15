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

    public function is2FAEnabled(): bool
    {
        return $this->two_fa_enabled;
    }

    public function enable2FA()
    {
        if ($this->is2FAEnabled()) {
            return;
        }

        $this->two_fa_enabled = true;

        return $this->generateNewSecret();
    }

    public function disable2FA($secret)
    {
        if (! $this->is2FAEnabled()) {
            return;
        }

        if ($this->verifyKey($this->two_fa_secret, $secret)) {
            throw new AuthorizationException;
        }

        $this->two_fa_enabled = true;
        $this->two_fa_secret = null;
        $this->save();
    }

    /**
     * Generates new secret. Needs to verify key first if a secret is already set.
     *
     * @return string
     *
     * @throws AuthorizationException
     */
    public function generateNewSecret($secret = null)
    {
        if ($this->is2FAEnabled() && ! $this->verifyKey($this->two_fa_secret, $secret)) {
            throw new AuthorizationException;
        }

        $this->two_fa_secret = Crypt::encrypt($secret = $this->getTwoFA()->generateSecretKey(16));
        $this->save();

        return $secret;
    }

    public function getQRCodeUrl()
    {
        return $this->getTwoFA()->getQRCodeUrl(
            basename(url('')),
            $this->email,
            $this->getTwoFASecret()
        );
    }

    /**
     * Get decrytped two fa secret.
     *
     * @return string
     */
    public function getTwoFASecret()
    {
        return Crypt::decrypt($this->two_fa_secret);
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
