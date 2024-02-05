<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class ReCaptchaV3 implements ValidationRule
{
    public function __construct(
        private ?string $action = null,
        private ?float $minScore = null
    ) {

    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $siteVerify = Http::asForm()
            ->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => config('services.recaptcha.secret_key'),
                'response' => $value,
            ]);

        if ($siteVerify->failed()) {
            $fail('Google reCAPTCHA was not able to verify the form, please try again.');
            return;
        }

        if ($siteVerify->successful()) {
            $body = $siteVerify->json();

            if ($body['success'] !== true) {
                $fail('Your form submission failed the Google reCAPTCHA verification, please try again.');
                return;
            }

            if (!is_null($this->action) && $this->action != $body['action']) {
                $fail('The action found in the form didn\'t match the Google reCAPTCHA action, please try again.');
                return;
            }

            if (!is_null($this->minScore) && $this->minScore > $body['score']) {
                $fail('The Google reCAPTCHA verification score was too low, please try again.');
                return;
            }
        }
    }
}
