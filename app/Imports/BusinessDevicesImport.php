<?php

namespace App\Imports;

use App\Models\ManualParty;
use App\Models\ManualProduct;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class BusinessDevicesImport extends StringValueBinder implements ToCollection, WithCustomValueBinder
{
    public const FIELDS = ['manual_product_id', 'vts_number', 'sim_number', 'sales_price'];

    public array $headers = [];

    public array $rows = [];

    private bool $loaded = false;

    public function collection(Collection $rows)
    {
        // Preview and import both read only the first worksheet.
        if ($this->loaded) {
            return;
        }
        $this->loaded = true;
        $this->headers = $rows->shift()?->map(fn ($value) => trim((string) $value))->all() ?? [];
        if (count($this->headers) < 4 || count($this->headers) > 100) {
            throw ValidationException::withMessages(['file' => 'Use 4 to 100 columns, with field names in the first row.']);
        }
        foreach ($rows as $row) {
            if ($row->filter(fn ($value) => $value !== null && $value !== '')->isEmpty()) {
                continue;
            }
            if (count($this->rows) >= 1000) {
                throw ValidationException::withMessages(['file' => 'Import a maximum of 1000 device rows at a time.']);
            }
            $this->rows[] = $row->map(fn ($value) => trim((string) $value))->values()->all();
        }
        if (! count($this->rows) || count($this->rows) > 1000) {
            throw ValidationException::withMessages(['file' => 'The first worksheet must contain between 1 and 1000 device rows.']);
        }
    }

    public function suggestedMapping(): array
    {
        $aliases = [
            'manual_party_id' => ['manual_party_id', 'party_id', 'party', 'party_name', 'customer', 'customer_name'],
            'manual_product_id' => ['manual_product_id', 'product_id', 'device', 'model', 'product', 'device_model', 'product_name'],
            'vts_number' => ['vts_number', 'vts', 'imei', 'imei_number', 'device_imei'],
            'sim_number' => ['sim_number', 'sim', 'sim_no', 'sim_card', 'iccid'],
            'sales_price' => ['sales_price', 'sale_price', 'price', 'selling_price', 'amount'],
        ];
        $mapping = [];
        foreach ($this->headers as $index => $header) {
            $normalized = trim(preg_replace('/[^a-z0-9]+/', '_', strtolower($header)), '_');
            foreach ($aliases as $field => $names) {
                if (in_array($normalized, $names, true) && ! array_key_exists($field, $mapping)) {
                    $mapping[$field] = $index;
                }
            }
        }

        return $mapping;
    }

    public function devices(?array $mapping = null, bool $perRowParty = false): array
    {
        $fields = $perRowParty ? array_merge(['manual_party_id'], self::FIELDS) : self::FIELDS;
        $mapping ??= array_intersect_key($this->suggestedMapping(), array_flip($fields));
        $rules = ['mapping' => ['required', 'array:'.implode(',', $fields)]];
        foreach ($fields as $field) {
            $rules['mapping.'.$field] = ['required', 'integer', 'min:0', 'max:'.(count($this->headers) - 1), 'distinct'];
        }
        $mapping = Validator::make(['mapping' => $mapping], $rules)->validate()['mapping'];
        if (count(array_unique(array_map('intval', $mapping))) !== count($fields)) {
            throw ValidationException::withMessages(['mapping' => 'Select a different file column for each system field.']);
        }
        $products = ManualProduct::all(['id', 'name']);
        $parties = $perRowParty ? ManualParty::all(['id', 'name']) : collect();
        $errors = [];

        $devices = [];
        foreach ($this->rows as $index => $row) {
            $device = [];
            foreach ($fields as $field) {
                $device[$field] = $row[(int) $mapping[$field]] ?? '';
            }
            if ($perRowParty && $device['manual_party_id'] !== '' && ! ctype_digit($device['manual_party_id'])) {
                $partyName = $device['manual_party_id'];
                $matches = $parties->filter(fn ($party) => mb_strtolower($party->name) === mb_strtolower($partyName));
                if ($matches->count() === 1) {
                    $device['manual_party_id'] = (string) $matches->first()->id;
                } else {
                    $errors['devices.'.$index.'.manual_party_id'] = $matches->isEmpty()
                        ? 'Party "'.$partyName.'" was not found. Use an existing Manual Party ID or name.'
                        : 'More than one party is named "'.$partyName.'". Use its Party ID instead.';
                }
            }
            if (! ctype_digit($device['manual_product_id'])) {
                $matches = $products->filter(fn ($product) => mb_strtolower($product->name) === mb_strtolower($device['manual_product_id']));
                if ($matches->count() === 1) {
                    $device['manual_product_id'] = (string) $matches->first()->id;
                }
            }

            $devices[] = $device;
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        return $devices;
    }
}
