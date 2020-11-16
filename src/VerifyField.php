<?php

namespace Litstack\TwoFA;

use Ignite\Crud\BaseField;
use Ignite\Crud\Fields\Traits\FieldHasRules;
use Illuminate\Validation\Rule;

class VerifyField extends BaseField
{
    use FieldHasRules;

    /**
     * Field Vue component.
     *
     * @var string
     */
    protected $component = 'lit-field-verify';

    /**
     * Mount one time password field.
     *
     * @return void
     */
    public function mount()
    {
        $this->rules(...$this->getVerificationRules());
    }

    /**
     * Get verification rules.
     *
     * @return array
     */
    protected function getVerificationRules()
    {
        $required = Rule::requiredIf(function () {
            if (request()->has('payload') && array_key_exists($this->id, request()->payload)) {
                return true;
            }

            response()
                ->json([
                    'message' => __lit('2fa.messages.please-verify'),
                    'errors'  => [$this->id => ['required']],
                ])
                ->setStatusCode(422)
                ->send();
        });

        if (! lit_user() instanceof Authenticatable) {
            return [$required, 'password:'.config('lit.guard')];
        }

        if (! lit_user()->is2FAEnabled()) {
            return [$required, 'password:'.config('lit.guard')];
        }

        return [$required, new TwoFARule];
    }
}
