<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class GeralNotification extends Notification
{
    use Queueable;

    public function __construct(
        public $autor,
        public string $tipo,
        public string $mensagem,
        public string $url,
        public ?string $tituloPost = null
    ) {}

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'tipo'           => $this->tipo,
            'autor_id'       => $this->autor->id_usuario,
            'autor_nome'     => $this->autor->nome ?? $this->autor->nome_usuario,
            'autor_username' => $this->autor->nome_usuario,
            'autor_foto'     => $this->autor->perfil?->foto,
            'mensagem'       => $this->mensagem,
            'url'            => $this->url,
            'titulo_post'    => $this->tituloPost,
        ];
    }
}