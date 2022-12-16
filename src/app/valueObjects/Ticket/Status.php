<?php

namespace App\ValueObjects\Ticket;

class Status
{
    const NEW = 1;
    const IN_PROGRESS = 2;
    const FINISHED = 3;
    const CANCELED = 4;

    public function label($status): string
    {
        return match ($status) {
            Status::NEW => 'Novo',
            Status::IN_PROGRESS => 'Em andamento',
            Status::FINISHED => 'Finalizado',
            Status::CANCELED => 'Cancelado'
        };
    }
}
