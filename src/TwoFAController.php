<?php

namespace Litstack\TwoFA;

class TwoFAController
{
    /**
     * Request 2 factor authentication.
     *
     * @param  TwoFARequest $request
     * @return void
     */
    public function request(TwoFARequest $request)
    {
        $user = lit_user();

        if ($user->two_fa_enabled) {
            abort(405, '2 Factor Authentication already enabled.');
        }

        return response()->json([
            'secret' => $user->generateNewSecret(),
            'qr'     => $user->getQRCodeUrl(),
        ]);
    }

    /**
     * Activate 2 factor authentication.
     *
     * @param  TwoFAChangeRequest $request
     * @return void
     */
    public function activate(TwoFAChangeRequest $request)
    {
        lit_user()->enable2FA();
    }
}
