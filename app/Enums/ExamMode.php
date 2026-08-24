<?php

namespace App\Enums;

enum ExamMode: string
{
    case NgauNhien    = 'ngau_nhien';
    case TheoDoKho    = 'theo_do_kho';
    case TyLeTuyChon  = 'ty_le_tuy_chon';

    public function nhanHien(): string
    {
        return match($this) {
            self::NgauNhien   => 'Ngẫu nhiên',
            self::TheoDoKho   => 'Theo độ khó',
            self::TyLeTuyChon => 'Tỷ lệ tùy chỉnh',
        };
    }
}
