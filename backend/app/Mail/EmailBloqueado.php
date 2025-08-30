<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailIrregular extends Mailable
{
    use Queueable, SerializesModels;

    public $nome;

    /**
     * Create a new message instance.
     */
    public function __construct($nome)
    {
        $this->nome = $nome;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Situação Irregular Detectada')
                    ->markdown('emails.irregular')
                    ->with([
                        'nome' => $this->nome,
                    ]);
    }
}
