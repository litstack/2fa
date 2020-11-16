<?php

namespace Tests;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\User;
use Litstack\TwoFA\HasTwoFactorAuthentication;
use Litstack\TwoFA\TwoFA;
use Mockery as m;

class HasTwoFactorAuthenticationTest extends IntegrationTestCase
{
    /** @test */
    public function test_getTwoFA_method_returns_2fa_binding()
    {
        $twofa = m::mock(TwoFA::class);
        $this->app->bind('lit.2fa', fn () => $twofa);

        $this->assertSame($twofa, (new DummyTwoFAUser)->getTwoFA());
    }

    /** @test */
    public function test_is2FAEnabled_method()
    {
        $user = new DummyTwoFAUser;

        $user->two_fa_enabled = true;
        $this->assertTrue($user->is2FAEnabled());
        $user->two_fa_enabled = false;
        $this->assertFalse($user->is2FAEnabled());
    }

    /** @test */
    public function test_enable2FA_method()
    {
        $user = DummyTwoFAUser::create();

        $this->assertFalse($user->is2FAEnabled());
        $user->enable2FA();
        $this->assertTrue($user->is2FAEnabled());
    }

    /** @test */
    public function test_disable2FA_disables_2FA()
    {
        $user = DummyTwoFAUser::create();

        $user->generateNewSecret();
        $user->enable2FA();

        $user->disable2FA($user->getCurrentOtp());

        $this->assertFalse($user->is2FAEnabled());
        $this->assertNull($user->getTwoFASecret());
    }

    /** @test */
    public function test_disable2FA_fails_when_incorrect_secret_is_given()
    {
        $user = DummyTwoFAUser::create();

        $user->generateNewSecret();
        $user->enable2FA();

        $this->expectException(AuthorizationException::class);

        $user->disable2FA('foo');
    }

    /** @test */
    public function test_generateNewSecret_saves_new_secret()
    {
        $user = DummyTwoFAUser::create();

        $secret = $user->generateNewSecret();
        $this->assertSame($secret, $user->refresh()->getTwoFASecret());
    }

    /** @test */
    public function test_generateNewSecret_fails_when_incorrect_secret_is_given()
    {
        $user = DummyTwoFAUser::create();

        $user->generateNewSecret();
        $user->enable2FA();

        $this->expectException(AuthorizationException::class);

        $user->generateNewSecret();
    }

    /** @test */
    public function test_getCurrentOtp_method()
    {
        $user = DummyTwoFAUser::create();

        $this->assertNull($user->getCurrentOtp());

        $user->generateNewSecret();

        $this->assertSame(6, strlen($user->getCurrentOtp()));
    }

    /** @test */
    public function test_getQRCodeUrl_method()
    {
        $user = DummyTwoFAUser::create();

        $this->assertNull($user->getQRCodeUrl());

        $user->generateNewSecret();

        $this->assertIsString($user->getQRCodeUrl());
    }
}

class DummyTwoFAUser extends User
{
    use HasTwoFactorAuthentication;

    public $table = 'lit_users';

    public $timestamps = false;

    protected $hidden = ['two_fa_secret'];

    protected $casts = [
        'two_fa_enabled' => 'boolean',
    ];
}
