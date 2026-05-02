<?php

namespace App\Services;

use App\Models\ActivationRequest;
use App\Models\AddCustomerVehicle;
use App\Models\CheckComplain;
use App\Models\DeleteData;
use App\Models\GpsCard;
use App\Models\KycRecharge;
use App\Models\ProductMaster;
use App\Models\RechargeRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class GpsCardLookupService
{
    public function findByCardNumber(string $cardNumber): ?array
    {
        $gpsCard = GpsCard::with([
            'productModel',
            'createdBy.roles',
            'usedBy.roles',
            'printedBy.roles',
            'assignedActivationRequest',
        ])
            ->withoutGlobalScopes()
            ->withTrashed()
            ->where('card_number', $cardNumber)
            ->first();

        if (! $gpsCard) {
            return null;
        }

        $activation = $this->findActivation($gpsCard);
        $vehicle = $this->findVehicle($activation);
        $user = $this->findUser($activation, $vehicle, $gpsCard);
        $productMaster = $this->findProductMaster($activation, $vehicle);
        $productModel = $productMaster?->product_model ?? $gpsCard->productModel;

        $rechargeHistory = $this->findRechargeHistory($vehicle);
        $complaints = $this->findComplaints($vehicle);
        $kycRecords = $this->findKycRecords($vehicle, $user);
        $deleteRecord = $this->findDeleteRecord($vehicle, $productMaster);

        $serviceValidity = $this->buildServiceValidity(
            $activation,
            $vehicle,
            $productModel,
            $rechargeHistory->isNotEmpty()
        );

        $firstRecharge = $rechargeHistory->sortBy(fn ($row) => $this->resolveDate($row->payment_date ?? $row->created_at)?->timestamp ?? 0)->first();
        $latestRecharge = $rechargeHistory->first();
        $latestKyc = $kycRecords->first();
        $latestComplaint = $complaints->first();

        $nextRecharge = $this->buildNextRechargeSummary($serviceValidity, $firstRecharge, $latestRecharge);
        $documents = $this->collectDocuments($activation, $vehicle);
        $serviceDeadline = $this->resolveNearestDeadline($serviceValidity);

        return [
            'gpsCard' => $gpsCard,
            'activation' => $activation,
            'vehicle' => $vehicle,
            'user' => $user,
            'productMaster' => $productMaster,
            'productModel' => $productModel,
            'rechargeHistory' => $rechargeHistory,
            'firstRecharge' => $firstRecharge,
            'latestRecharge' => $latestRecharge,
            'kycRecords' => $kycRecords,
            'latestKyc' => $latestKyc,
            'complaints' => $complaints,
            'latestComplaint' => $latestComplaint,
            'deleteRecord' => $deleteRecord,
            'serviceValidity' => $serviceValidity,
            'nextRecharge' => $nextRecharge,
            'serviceDeadline' => $serviceDeadline,
            'documents' => $documents,
            'stats' => [
                'recharge_count' => $rechargeHistory->count(),
                'complaint_count' => $complaints->count(),
                'document_count' => $documents->count(),
                'kyc_done' => (bool) $latestKyc,
            ],
            'meta' => [
                'card_number' => $cardNumber,
                'has_recharge_history' => $rechargeHistory->isNotEmpty(),
                'has_complaints' => $complaints->isNotEmpty(),
                'handled_by_available' => false,
            ],
        ];
    }

    private function findActivation(GpsCard $gpsCard): ?ActivationRequest
    {
        return ActivationRequest::with([
            'party_type',
            'select_party.roles',
            'select_party.state',
            'select_party.district',
            'createdBy.roles',
            'product_master.product_model',
            'product_master.imei',
            'product_master.vts',
            'vehicle_type',
            'state',
            'disrict',
            'app_link',
            'gpsCard',
            'vehicle',
        ])
            ->withoutGlobalScopes()
            ->withTrashed()
            ->where(function ($query) use ($gpsCard) {
                $query->where('gps_card_id', $gpsCard->id);

                if ($gpsCard->used_by_activation_request_id) {
                    $query->orWhere('id', $gpsCard->used_by_activation_request_id);
                }
            })
            ->latest('id')
            ->first();
    }

    private function findVehicle(?ActivationRequest $activation): ?AddCustomerVehicle
    {
        if (! $activation) {
            return null;
        }

        return AddCustomerVehicle::with([
            'select_vehicle_type',
            'product_master.product_model',
            'product_master.imei',
            'product_master.vts',
            'appLink',
            'creator.roles',
        ])
            ->withoutGlobalScopes()
            ->withTrashed()
            ->where(function ($query) use ($activation) {
                if ($activation->vehicle_id) {
                    $query->orWhere('id', $activation->vehicle_id);
                }

                if ($activation->id) {
                    $query->orWhere('activation_id', $activation->id);
                }

                if ($activation->vehicle_reg_no) {
                    $query->orWhere('vehicle_number', $activation->vehicle_reg_no);
                }
            })
            ->latest('id')
            ->first();
    }

    private function findUser(?ActivationRequest $activation, ?AddCustomerVehicle $vehicle, GpsCard $gpsCard): ?User
    {
        $userId = null;

        if ($activation?->select_party_id) {
            $userId = $activation->select_party_id;
        } elseif ($vehicle && is_numeric($vehicle->created_by_id)) {
            $userId = (int) $vehicle->created_by_id;
        } elseif ($gpsCard->used_by_id) {
            $userId = $gpsCard->used_by_id;
        }

        if (! $userId) {
            return $activation?->select_party ?? $vehicle?->creator ?? $gpsCard->usedBy;
        }

        return User::withTrashed()
            ->with(['roles', 'state', 'district', 'createdBy.roles'])
            ->find($userId);
    }

    private function findProductMaster(?ActivationRequest $activation, ?AddCustomerVehicle $vehicle): ?ProductMaster
    {
        $productId = $activation?->product_id ?: $vehicle?->product_id;

        if (! $productId) {
            return $activation?->product_master ?? $vehicle?->product_master;
        }

        return ProductMaster::withTrashed()
            ->with(['product_model', 'imei', 'vts'])
            ->find($productId);
    }

    private function findRechargeHistory(?AddCustomerVehicle $vehicle): Collection
    {
        if (! $vehicle?->vehicle_number) {
            return collect();
        }

        return RechargeRequest::withTrashed()
            ->withoutGlobalScopes()
            ->with(['select_recharge', 'user.roles', 'created_by.roles'])
            ->where('vehicle_number', $vehicle->vehicle_number)
            ->get()
            ->sortByDesc(fn ($row) => $this->resolveDate($row->payment_date ?? $row->created_at)?->timestamp ?? 0)
            ->values();
    }

    private function findComplaints(?AddCustomerVehicle $vehicle): Collection
    {
        if (! $vehicle) {
            return collect();
        }

        return CheckComplain::withTrashed()
            ->withoutGlobalScopes()
            ->with([
                'select_complains',
                'created_by.roles',
                'vehicle.product_master.product_model',
                'vehicle.product_master.imei',
                'vehicle.product_master.vts',
            ])
            ->where(function ($query) use ($vehicle) {
                $query->where('vehicle_id', $vehicle->id)
                    ->orWhere('vehicle_no', $vehicle->vehicle_number);
            })
            ->get()
            ->sortByDesc(fn ($row) => $this->resolveDate($row->updated_at ?? $row->created_at)?->timestamp ?? 0)
            ->values();
    }

    private function findKycRecords(?AddCustomerVehicle $vehicle, ?User $user): Collection
    {
        if (! $vehicle?->id && ! $vehicle?->vehicle_number && ! $user?->id) {
            return collect();
        }

        $baseQuery = KycRecharge::with(['user.roles', 'vehicle', 'createdBy.roles']);

        $vehicleRecords = collect();

        if ($vehicle?->id || $vehicle?->vehicle_number) {
            $vehicleRecords = (clone $baseQuery)
                ->where(function ($builder) use ($vehicle) {
                    if ($vehicle?->id) {
                        $builder->orWhere('vehicle_id', $vehicle->id);
                    }

                    if ($vehicle?->vehicle_number) {
                        $builder->orWhere('vehicle_number', $vehicle->vehicle_number);
                    }
                })
                ->get();
        }

        if ($vehicleRecords->isNotEmpty()) {
            return $vehicleRecords
                ->sortByDesc(fn ($row) => $this->resolveDate($row->payment_date ?? $row->created_at)?->timestamp ?? 0)
                ->values();
        }

        if (! $user?->id) {
            return collect();
        }

        return (clone $baseQuery)
            ->where('user_id', $user->id)
            ->get()
            ->sortByDesc(fn ($row) => $this->resolveDate($row->payment_date ?? $row->created_at)?->timestamp ?? 0)
            ->values();
    }

    private function findDeleteRecord(?AddCustomerVehicle $vehicle, ?ProductMaster $productMaster): ?DeleteData
    {
        $query = DeleteData::query()->where(function ($builder) use ($vehicle, $productMaster) {
            if ($vehicle?->vehicle_number) {
                $builder->orWhere('vehicle_no', $vehicle->vehicle_number);
            }

            if ($productMaster?->imei?->imei_number) {
                $builder->orWhere('imei_no', $productMaster->imei->imei_number);
            }

            if ($productMaster?->vts?->vts_number) {
                $builder->orWhere('vts_no', $productMaster->vts->vts_number);
            }
        });

        if (count($query->getQuery()->wheres ?? []) === 0) {
            return null;
        }

        return $query->latest('delete_date')->first();
    }

    private function buildServiceValidity(
        ?ActivationRequest $activation,
        ?AddCustomerVehicle $vehicle,
        mixed $productModel,
        bool $hasRechargeHistory
    ): array {
        $baseDate = $this->resolveDate(
            $activation?->request_date
            ?? $vehicle?->request_date
            ?? $activation?->created_at
            ?? $vehicle?->created_at
        );

        $now = now()->startOfDay();
        $validity = [];

        foreach (['amc' => 'AMC', 'warranty' => 'Warranty', 'subscription' => 'Subscription'] as $field => $label) {
            $months = (int) data_get($productModel, $field, 0);
            $expiryDate = null;
            $source = null;

            if ($hasRechargeHistory && $vehicle?->{$field}) {
                $expiryDate = $this->resolveDate($vehicle->{$field});
                $source = 'vehicle_expiry';
            } elseif ($baseDate && $months > 0) {
                $expiryDate = $baseDate->copy()->addMonths($months);
                $source = 'activation_plus_model_duration';
            } elseif ($vehicle?->{$field}) {
                $expiryDate = $this->resolveDate($vehicle->{$field});
                $source = 'vehicle_date';
            } elseif ($activation?->{$field}) {
                $expiryDate = $this->resolveDate($activation->{$field});
                $source = 'activation_date';
            }

            if (! $expiryDate) {
                continue;
            }

            $daysLeft = $now->diffInDays($expiryDate->copy()->startOfDay(), false);

            $validity[$field] = [
                'key' => $field,
                'label' => $label,
                'date' => $expiryDate,
                'days_left' => $daysLeft,
                'absolute_days' => abs($daysLeft),
                'expired' => $daysLeft < 0,
                'soon' => $daysLeft >= 0 && $daysLeft <= 30,
                'months' => $months,
                'source' => $source,
            ];
        }

        return $validity;
    }

    private function buildNextRechargeSummary(array $serviceValidity, ?RechargeRequest $firstRecharge, ?RechargeRequest $latestRecharge): ?array
    {
        $subscription = $serviceValidity['subscription'] ?? null;
        $serviceDeadline = $this->resolveNearestDeadline($serviceValidity);
        $target = $subscription ?? $serviceDeadline;

        if (! $target) {
            return null;
        }

        return [
            'due_date' => $target['date'],
            'days_left' => $target['days_left'],
            'expired' => $target['expired'],
            'label' => $target['label'],
            'first_plan_name' => $firstRecharge?->select_recharge?->plan_name,
            'first_recharge_date' => $this->resolveDate($firstRecharge?->payment_date ?? $firstRecharge?->created_at),
            'latest_plan_name' => $latestRecharge?->select_recharge?->plan_name,
            'latest_recharge_date' => $this->resolveDate($latestRecharge?->payment_date ?? $latestRecharge?->created_at),
            'latest_plan_amount' => $latestRecharge?->payment_amount,
            'latest_plan_type' => $latestRecharge?->select_recharge?->type,
        ];
    }

    private function resolveNearestDeadline(array $serviceValidity): ?array
    {
        return collect($serviceValidity)
            ->sortBy(fn ($row) => $row['date']?->timestamp ?? PHP_INT_MAX)
            ->first();
    }

    private function collectDocuments(?ActivationRequest $activation, ?AddCustomerVehicle $vehicle): Collection
    {
        $documents = collect();

        $mapping = [
            ['model' => $vehicle, 'collection' => 'id_proofs', 'label' => 'ID Proof', 'source' => 'Customer Vehicle'],
            ['model' => $vehicle, 'collection' => 'registration_certificate', 'label' => 'Registration Certificate', 'source' => 'Customer Vehicle'],
            ['model' => $vehicle, 'collection' => 'insurance', 'label' => 'Insurance', 'source' => 'Customer Vehicle'],
            ['model' => $vehicle, 'collection' => 'pollution', 'label' => 'Pollution Certificate', 'source' => 'Customer Vehicle'],
            ['model' => $vehicle, 'collection' => 'vehicle_photos', 'label' => 'Vehicle Photo', 'source' => 'Customer Vehicle'],
            ['model' => $vehicle, 'collection' => 'product_images', 'label' => 'Device Installation Photo', 'source' => 'Customer Vehicle'],
            ['model' => $activation, 'collection' => 'id_proofs', 'label' => 'Activation ID Proof', 'source' => 'Activation Request'],
            ['model' => $activation, 'collection' => 'customer_image', 'label' => 'Customer Photo', 'source' => 'Activation Request'],
            ['model' => $activation, 'collection' => 'vehicle_photos', 'label' => 'Activation Vehicle Photo', 'source' => 'Activation Request'],
            ['model' => $activation, 'collection' => 'product_images', 'label' => 'Activation Device Photo', 'source' => 'Activation Request'],
        ];

        foreach ($mapping as $item) {
            $model = $item['model'];

            if (! $model || ! method_exists($model, 'getMedia')) {
                continue;
            }

            foreach ($model->getMedia($item['collection']) as $media) {
                $documents->push([
                    'id' => $media->id,
                    'label' => $item['label'],
                    'source' => $item['source'],
                    'file_name' => $media->file_name,
                    'url' => $media->getUrl(),
                    'thumbnail' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                    'mime_type' => $media->mime_type,
                    'is_image' => str_starts_with((string) $media->mime_type, 'image/'),
                ]);
            }
        }

        return $documents->values();
    }

    private function resolveDate(mixed $value): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        if ($value instanceof Carbon) {
            return $value->copy();
        }

        return Carbon::parse($value);
    }
}
