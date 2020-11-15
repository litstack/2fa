<?php

namespace Litstack\TwoFA;

interface Authenticable
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

    public function is2FAEnabled(): bool;

    public function enable2FA();

    public function disable2FA($secret);
}
