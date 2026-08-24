<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NopBaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Kiểm tra lượt thi thuộc về người dùng hiện tại
        $luotThi = \App\Models\ExamAttempt::find($this->luot_thi_id);
        return $luotThi && $luotThi->nguoi_dung_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'luot_thi_id'              => ['required', 'integer', 'exists:luot_thi,id'],
            'dap_an'                   => ['required', 'array', 'min:1'],
            'dap_an.*.cau_hoi_id'      => ['required', 'integer', 'exists:cau_hoi,id'],
            'dap_an.*.lua_chon_id'     => ['nullable', 'integer', 'exists:lua_chon,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'luot_thi_id.required'          => 'Không xác định được lượt thi.',
            'luot_thi_id.exists'            => 'Lượt thi không tồn tại.',
            'dap_an.required'               => 'Vui lòng cung cấp đáp án.',
            'dap_an.*.cau_hoi_id.required'  => 'Thiếu mã câu hỏi.',
            'dap_an.*.cau_hoi_id.exists'    => 'Câu hỏi không tồn tại trong hệ thống.',
            'dap_an.*.lua_chon_id.exists'   => 'Lựa chọn đáp án không hợp lệ.',
        ];
    }
}
