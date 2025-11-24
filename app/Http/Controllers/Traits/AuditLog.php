<?php

namespace App\Traits;

trait AuditLog
{
    public function bootAuditLog()
    {
        static::created(function ($model) {
            \Log::info('Created: ' . get_class($model), $model->toArray());
        });

        static::updated(function ($model) {
            \Log::info('Updated: ' . get_class($model), $model->getChanges());
        });

        static::deleted(function ($model) {
            \Log::info('Deleted: ' . get_class($model), $model->toArray());
        });
    }
}
