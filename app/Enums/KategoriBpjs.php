<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum KategoriBpjs: string implements HasLabel
{
    case BPJS_KAPITASI = 'bpjs_kapitasi';
    case BPJS_NON_KAPITASI = 'bpjs_non_kapitasi';
    case LAPAD_RUHAMA = 'lapad_ruhama';
    case JASA_GIRO = 'jasa_giro';
    case LAIN_LAIN = 'lain_lain';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::BPJS_KAPITASI => 'BPJS Kapitasi',
            self::BPJS_NON_KAPITASI => 'BPJS Non Kapitasi',
            self::LAPAD_RUHAMA => 'Lapad Ruhama',
            self::JASA_GIRO => 'Jasa Giro',
            self::LAIN_LAIN => 'Lain-Lain',
        };
    }
}