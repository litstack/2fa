<?php

namespace Litstack\TwoFA;

interface Authenticatable
{
    /**
     * Generates new secret. Needs to verify key first if a secret is already set.
     *
     * @param  string|null $secret
     * @return void
     */
    public function generateNewSecret($secret = null);

    /**
     * Verify key.
     *
     * @param  string $key
     * @return bool
     */
    public function verifyKey($secret): bool;

    /**
     * Get TwoFA instance.
     *
     * @return TwoFA
     */
    public function getTwoFA(): TwoFA;

    /**
     * Determines if 2 factor authentication is enabled.
     *
     * @return bool
     */
    public function is2FAEnabled(): bool;

    /**
     * Enabled 2 factor authentication.
     *
     * @return void
     */
    public function enable2FA();

    /**
     * Disable 2 factor authentication.
     *
     * @param  string $secret
     * @return void
     */
    public function disable2FA($secret);
}
