<?php

declare(strict_types=1);

namespace Thehouseofel\Kalion\Core\Infrastructure\Helpers;

use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

final class MyMailDM
{
    public function __construct(protected Mailable $mailable, $keepOriginalRecipientsEvenInTests = false)
    {
        if (config('kalion.mail_active_tests') && ! $keepOriginalRecipientsEvenInTests) {
            $arrayTo             = static::getRecipientsFromStringVariable(config('kalion.mail_test_recipients'));
            $this->mailable->to  = [];
            $this->mailable->cc  = [];
            $this->mailable->bcc = [];
            $this->mailable->to($arrayTo);
        }
    }

    public function send()
    {
        if (! config('kalion.mail_is_active')) {
            return;
        }
        if (empty($this->mailable->to)) {
            abort_d(500, 'A la clase mailable le faltan los destinatarios del correo.');
        }
        if (empty($this->mailable->subject)) {
            abort_d(500, 'A la clase mailable le falta el Asunto del mail.');
        }
        Mail::send($this->mailable);
    }

    public function sendToRecipients($recipients)
    {
        if (! config('kalion.mail_is_active')) {
            return null;
        }
        Mail::to($recipients)->send($this->mailable);
    }

    public function returnMailView()
    {
        return $this->mailable;
    }

    public function getRecipientsTo()
    {
        return legacy_json_to_object($this->mailable->to);
    }

    public function getRecipientsCc()
    {
        return legacy_json_to_object($this->mailable->cc);
    }

    public static function getRecipientsFromStringVariable($stringRecipients)
    {
        if (empty($stringRecipients)) {
            return [];
        }

        $testRecipients = collect(explode(',', $stringRecipients));

        $formatted_mailRecipients = $testRecipients->filter(function ($value, $key) {
            return ! str_starts_with($value, 'u2d');
        })->map(function ($item, $key) {
            return ['name' => null, 'email' => $item];
        })->values()->all();
        $u2dRecipients            = $testRecipients->filter(function ($value, $key) {
            return str_starts_with($value, 'u2d');
        })->all();
        $formatted_u2dRecipients  = (new UserModelManager())->getUsersInField($u2dRecipients, 'matricula_dacfi')->pluck('array_name_and_email')->toArray();

        return array_merge($formatted_u2dRecipients, $formatted_mailRecipients);
    }

}
