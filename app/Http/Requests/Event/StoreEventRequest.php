<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'image' => 'nullable|image|max:2048',
            'min_participants' => 'required|integer|min:1',
            'max_participants' => 'required|integer|min:1|gte:min_participants',
            'event_date' => 'required|date|after:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'address' => 'required|string|max:255',
            'voting_expiry_time' => 'required|date|after:today|before:event_date',
            'event_type' => 'sometimes|in:sport,meetup,travel,custom',
        ];
    }

    public function messages()
    {
        return [
            'max_participants.gte' => 'Maksimal ishtirokchilar soni minimal ishtirokchilar sonidan katta yoki teng bo\'lishi kerak.',
            'event_date.after' => 'Tadbir sanasi kelajakdagi sana bo\'lishi kerak.',
            'voting_expiry_time.after' => 'Ovoz berish muddati kelajakdagi sana bo\'lishi kerak.',
            'voting_expiry_time.before' => 'Ovoz berish muddati tadbir sanasidan oldin bo\'lishi kerak.',
        ];
    }
}
