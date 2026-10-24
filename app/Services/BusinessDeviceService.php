<?php

namespace App\Services;

use App\Models\BusinessDevice;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BusinessDeviceService
{
    public function rules(int $quantity, ?BusinessDevice $device = null): array
    {
        return [
            'manual_party_id' => ['required', 'integer', Rule::exists('manual_parties', 'id')->whereNull('deleted_at')],
            'assigned_at' => ['required', 'date_format:Y-m-d'],
            'quantity' => ['required', 'integer', 'min:1', 'max:1000'],
            'devices' => ['required', 'array', 'size:'.$quantity],
            'devices.*' => ['required', 'array:manual_product_id,vts_number,sim_number,sales_price'],
            'devices.*.manual_product_id' => ['required', 'integer', Rule::exists('manual_products', 'id')->whereNull('deleted_at')],
            'devices.*.vts_number' => ['required', 'string', 'max:100', 'distinct', Rule::unique('business_devices', 'vts_number')->ignore($device?->id)],
            'devices.*.sim_number' => ['required', 'string', 'max:100'],
            'devices.*.sales_price' => ['required', 'numeric', 'min:0', 'max:9999999999.99', 'decimal:0,2'],
        ];
    }

    public function store(array $data, int $userId): void
    {
        DB::transaction(function () use ($data, $userId) {
            foreach ($data['devices'] as $row) {
                BusinessDevice::create(array_merge($row, [
                    'manual_party_id' => $row['manual_party_id'] ?? $data['manual_party_id'],
                    'assigned_at' => $data['assigned_at'],
                    'created_by' => $userId,
                ]));
            }
        });
    }

    public function importRules(int $quantity, bool $perRowParty): array
    {
        $rules = $this->rules($quantity);
        if ($perRowParty) {
            unset($rules['manual_party_id']);
            $rules['devices.*'] = ['required', 'array:manual_party_id,manual_product_id,vts_number,sim_number,sales_price'];
            $rules['devices.*.manual_party_id'] = ['required', 'integer', Rule::exists('manual_parties', 'id')->whereNull('deleted_at')];
        }

        return $rules;
    }
}
