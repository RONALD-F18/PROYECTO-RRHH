<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactoRequest;
use App\Services\MailService;

class ContactoController extends Controller
{
    public function __construct(protected MailService $mailService)
    {
    }

    /**
     * Formulario público de contacto (landing). Envía correo al administrador.
     */
    public function enviar(ContactoRequest $request)
    {
        $data = $request->validated();

        $this->mailService->sendContactForm(
            destinatario: config('rrhh.contacto_email', 'ronaldacademy223@gmail.com'),
            nombre: $data['nombre'],
            email: $data['email'],
            asunto: $data['asunto'],
            mensaje: $data['mensaje'],
        );

        return response()->json([
            'success' => true,
            'message' => 'Mensaje enviado correctamente. Te responderemos pronto.',
        ], 200);
    }
}
