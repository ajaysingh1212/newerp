<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Resources\Json\JsonResource;

class ActivationRequestResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer_name,
            'mobile_number' => $this->mobile_number,
            'whatsapp_number' => $this->whatsapp_number,
            'email' => $this->email,
            'address' => $this->address,
            'request_date' => $this->request_date,
            'vehicle_model' => $this->vehicle_model,
            'vehicle_reg_no' => $this->vehicle_reg_no,
            'chassis_number' => $this->chassis_number,
            'engine_number' => $this->engine_number,
            'vehicle_color' => $this->vehicle_color,
            'status' => $this->status,
            'status_label' => $this->status_label,
            'amc' => $this->amc,
            'warranty' => $this->warranty,
            'subscription' => $this->subscription,
            'fitter_name' => $this->fitter_name,
            'fitter_number' => $this->fitter_number,

            // Relations
            'party_type' => $this->party_type ? [
                'id' => $this->party_type->id,
                'title' => $this->party_type->title ?? $this->party_type->name,
            ] : null,

            'select_party' => $this->select_party ? [
                'id' => $this->select_party->id,
                'name' => $this->select_party->name,
                'email' => $this->select_party->email,
                'number' => $this->select_party->number,
            ] : null,

            'product' => $this->product ? [
                'id' => $this->product->id,
                'name' => $this->product->product_name ?? $this->product->name,
            ] : null,

            'state' => $this->state ? [
                'id' => $this->state->id,
                'name' => $this->state->name,
            ] : null,

            'district' => $this->disrict ? [
                'id' => $this->disrict->id,
                'name' => $this->disrict->name,
            ] : null,

            'vehicle_type' => $this->vehicle_type ? [
                'id' => $this->vehicle_type->id,
                'name' => $this->vehicle_type->name,
            ] : null,

            'team' => $this->team ? [
                'id' => $this->team->id,
                'name' => $this->team->name,
            ] : null,

            'created_by' => $this->created_by ? [
                'id' => $this->created_by->id,
                'name' => $this->created_by->name,
                'email' => $this->created_by->email,
            ] : null,

            'app_link' => $this->app_link ? [
                'id' => $this->app_link->id,
                'title' => $this->app_link->title,
                'link' => $this->app_link->link,
            ] : null,

            'product_master' => $this->product_master ? [
                'id' => $this->product_master->id,
                'name' => $this->product_master->name,
            ] : null,

            // Media
            'id_proofs' => $this->id_proofs ? [
                'url' => $this->id_proofs->getUrl(),
                'thumb' => $this->id_proofs->getUrl('thumb'),
                'preview' => $this->id_proofs->getUrl('preview'),
            ] : null,

            'customer_image' => $this->customer_image ? [
                'url' => $this->customer_image->getUrl(),
                'thumb' => $this->customer_image->getUrl('thumb'),
                'preview' => $this->customer_image->getUrl('preview'),
            ] : null,

            'vehicle_photos' => $this->vehicle_photos ? [
                'url' => $this->vehicle_photos->getUrl(),
                'thumb' => $this->vehicle_photos->getUrl('thumb'),
                'preview' => $this->vehicle_photos->getUrl('preview'),
            ] : null,

            'product_images' => $this->product_images ? [
                'url' => $this->product_images->getUrl(),
                'thumb' => $this->product_images->getUrl('thumb'),
                'preview' => $this->product_images->getUrl('preview'),
            ] : null,
        ];
    }
}
