<?php

namespace Modules\Wallet\Transformers;
use App\Models\Setting;
use Carbon\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $activity_data = json_decode($this->activity_data);
        $timezone = Setting::where('name', 'default_time_zone')->value('val') ?? 'UTC';
        $datetime = Carbon::parse($this->datetime)->setTimezone($timezone)->format('Y-m-d H:i:s');

        return [
            'title' => $this->activity_message,
            'amount' => $activity_data->amount,
            'credit_debit_amount' => $activity_data->credit_debit_amount,
            'transaction_type' => $activity_data->transaction_type,
            'date' => $datetime,
        ];
    }
}
