<?php

namespace Litstack\TwoFA;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class TwoFARule implements Rule
{
    /**
     * Authenticated user.
     *
     * @var Authenticable
     */
    protected $user;

    /**
     * Create a new rule instance.
     *
     * @param  Authenticable $user
     * @return void
     */
    public function __construct(Authenticable $user = null)
    {
        $this->user = $user;

        if (is_null($this->user)) {
            $this->user = Auth::guard(config('lit.guard'))->user();
        }
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (! $this->user) {
            return false;
        }

        return $this->user->verifyKey($value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The code is incorrect.';
    }
}
