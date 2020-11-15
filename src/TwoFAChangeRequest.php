<?php

namespace Litstack\TwoFA;

class TwoFAChangeRequest extends TwoFARequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => ['required', 'string', 'password:'.config('lit.guard')],
            'code'     => ['required', 'string', new TwoFARule()],
        ];
    }
}
