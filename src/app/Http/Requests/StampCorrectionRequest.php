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
            'break_in.*' => ['nullable'],
            'break_out.*' => ['nullable'],
            'note' => ['required'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $checkIn = $this->input('check_in');
            $checkOut = $this->input('check_out');
            $breakIns = $this->input('break_in', []);
            $breakOuts = $this->input('break_out', []);

            foreach ($breakIns as $index => $breakIn) {
                $breakOut = $breakOuts[$index] ?? null;

                if (!$breakIn) continue;

                if ($breakIn < $checkIn || ($checkOut && $breakIn > $checkOut)) {
                    $validator->errors()->add("break_in.{$index}", '休憩時間が不適切な値です');
                }

                if ($breakOut) {
                    if ($breakOut <= $breakIn) {
                        $validator->errors()->add("break_out.{$index}", '休憩時間もしくは退勤時間が不適切な値です');
                    }

                    if ($checkOut && $breakOut > $checkOut) {
                        $validator->errors()->add("break_out.{$index}", '休憩時間もしくは退勤時間が不適切な値です');
                    }
                }
            }
        });
    }

    public function messages()
    {
        return [
            'check_out.after' => '出勤時間もしくは退勤時間が不適切な値です',
            'note.required' => '備考を記入してください',
        ];
    }
}