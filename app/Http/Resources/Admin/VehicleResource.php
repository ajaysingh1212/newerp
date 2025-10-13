<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'vehicle_number' => $this->vehicle_number,
            'owners_name' => $this->owners_name,
            'chassis_number' => $this->chassis_number,
            'vehicle_model' => $this->vehicle_model,
            'vehicle_color' => $this->vehicle_color,
            'engine_number' => $this->engine_number,
            'status' => $this->status,
            'amc' => $this->amc,
            'warranty' => $this->warranty,
            'subscription' => $this->subscription,
            'request_date' => $this->request_date,
            'activation_id' => $this->activation_id,
            'app_link_id' => $this->app_link_id,
            'product_id' => $this->product_id,
            'team_id' => $this->team_id,
            'created_by_id' => $this->created_by_id,
            'activated' => $this->activated,
            'insurance_expiry_date' => $this->insurance_expiry_date,
            'created_at' => optional($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => optional($this->updated_at)->format('Y-m-d H:i:s'),

            'vehicle_type' => optional($this->select_vehicle_type)->name,

            'product' => $this->product_master ? [
                'id' => $this->product_master->id,
                'imei' => $this->product_master->imei,
                'sim_number' => $this->product_master->sim_number,
                'sku' => $this->product_master->sku,
                'type' => $this->product_master->type,
                'status' => $this->product_master->status,

                'product_model' => $this->product_master->product_model ? [
                    'id' => $this->product_master->product_model->id,
                    'product_model' => $this->product_master->product_model->product_model,
                    'warranty' => $this->product_master->product_model->warranty,
                    'subscription' => $this->product_master->product_model->subscription,
                    'amc' => $this->product_master->product_model->amc,
                    'mrp' => $this->product_master->product_model->mrp,
                    'cnf_price' => $this->product_master->product_model->cnf_price,
                    'distributor_price' => $this->product_master->product_model->distributor_price,
                    'dealer_price' => $this->product_master->product_model->dealer_price,
                    'customer_price' => $this->product_master->product_model->customer_price,
                    'status' => $this->product_master->product_model->status,
                ] : null,
            ] : null,

            'app_link' => $this->appLink ? [
            'id' => $this->appLink->id,
            'title' => $this->appLink->title,
            'link' => $this->appLink->link,
            ] : null,


            'vehicle_photos' => optional($this->vehicle_photos)->url,
            'id_proofs' => optional($this->id_proofs)->url,
            'insurance' => $this->insurance ? $this->insurance->pluck('url') : [],
            'pollution' => $this->pollution ? $this->pollution->pluck('url') : [],
            'registration_certificate' => $this->registration_certificate ? $this->registration_certificate->pluck('url') : [],
            'product_images' => $this->product_images ? $this->product_images->pluck('url') : [],
        ];
    }
}