<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class StockHistory extends Model
{
    // Add fillable properties to allow mass assignment
    protected $fillable = [
        'activation_request_id',
        'customer_name',
        'mobile_number',
        'email',
        'whatsapp',
        'state',
        'district',
        'full_address',
        'request_date',
        'fitter_name',
        'fitter_number',
        'select_party_id',
        'vehicle_type',
        'vehicle_reg_no',
        'engine_number',
        'chassis_number',
        'vehicle_color',
        'product_name',
        'product_id',
        'select_party_id',
        'created_by_id',
        'vehicle_id',
        'status',
       
        // add any other columns you need
    ];

    public static function fromActivation(ActivationRequest $request): array
    {
        return [
            'activation_request_id' => $request->id,    // Note corrected field name
            'customer_name'         => $request->customer_name,
            'mobile_number'         => $request->mobile_number,
            'email'                 => $request->email,
            'state'                 => $request->state_id,
            'district'              => $request->disrict_id, // fixed typo disrict -> district
            'full_address'          => $request->address,
            'request_date'          => $request->request_date,
            'fitter_name'           => $request->fitter_name,
            'fitter_number'         => $request->fitter_number,
            'party_type'            => $request->party_type_id,
            'select_party_id'        => $request->select_party_id,
            'vehicle_model'         => $request->vehicle_model,
            'vehicle_type'          => $request->vehicle_type_id,
            'vehicle_reg_no'        => $request->vehicle_reg_no,
            'engine_number'         => $request->engine_number,
            'chassis_number'        => $request->chassis_number,
            'vehicle_color'         => $request->vehicle_color,
            'product_id'          => $request->product_id,
            'created_by_id'         => $request->created_by_id,
            'vehicle_id'         => $request->vehicle_id,
            'status'               => $request->status,

        ];
    }

    // In StockHistory.php
public function vehicle()
{
    return $this->belongsTo(AddCustomerVehicle::class, 'vehicle_id');
}

}
