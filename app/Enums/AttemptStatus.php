<?php

namespace App\Enums;

enum AttemptStatus: string
{
    case DangLam    = 'dang_lam';
    case HoanThanh  = 'hoan_thanh';
    case BoCuoc     = 'bo_cuoc';

    public function nhanHien(): string
    {
        return match($this) {
            self::DangLam   => 'Đang làm',
            self::HoanThanh => 'Hoàn thành',
            self::BoCuoc    => 'Bỏ cuộc',
        };
    }
}
