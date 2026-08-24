<?php

namespace App\Enums;

enum DifficultyLevel: string
{
    case De        = 'de';
    case TrungBinh = 'trung_binh';
    case Kho       = 'kho';

    public function nhanHien(): string
    {
        return match($this) {
            self::De        => 'Dễ',
            self::TrungBinh => 'Trung bình',
            self::Kho       => 'Khó',
        };
    }

    public function mauSac(): string
    {
        return match($this) {
            self::De        => 'green',
            self::TrungBinh => 'yellow',
            self::Kho       => 'red',
        };
    }
}
