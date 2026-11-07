<?php

namespace App\Enums;

enum TipoEvento: string
{
    case VagaOcupada = 'vaga_ocupada';
    case VagaLiberada = 'vaga_liberada';
    case CancelaAberta = 'cancela_aberta';
    case CancelaFechada = 'cancela_fechada';
}
