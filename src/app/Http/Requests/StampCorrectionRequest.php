<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StampCorrectionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'check_in' => ['required'],
            'check_out' => ['required', 'after:check_in'],
            'break_in.*' => ['nullable', 'after_or_equal:check_in', 'before_or_equal:check_out'],
            'break_out.*' => ['nullable', 'after_or_equal:check_in', 'before_or_equal:check_out'],
            'note' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'check_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'break_in.*.after_or_equal' => '休憩時間が不適切な値です',
            'break_in.*.before_or_equal' => '休憩時間が不適切な値です',
            'break_out.*.before_or_equal' => '休憩時間もしくは退勤時間が不適切な値です',
            'break_out.*.after_or_equal' => '休憩時間もしくは退勤時間が不適切な値です',
            'note.required' => '備考を記入してください',
        ];
    }
}