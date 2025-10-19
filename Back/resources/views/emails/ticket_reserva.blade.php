@component('mail::message')
# ¡Reserva Confirmada!

Hola **{{ $reserva->user->name }}**, aquí tienes los detalles de tu "ticket" de reserva:

@component('mail::panel')
**Cancha:** {{ $reserva->cancha->nombre }}  
**Fecha:** {{ date('d/m/Y', strtotime($reserva->fecha)) }}  
**Hora:** {{ date('H:i', strtotime($reserva->hora_inicio)) }}
@endcomponent

Por favor, llega con 10 minutos de anticipación.

¡Gracias por tu reserva!
@endcomponent