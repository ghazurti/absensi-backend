<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logAction('created');
        });

        static::updated(function ($model) {
            $model->logAction('updated');
        });

        static::deleted(function ($model) {
            $model->logAction('deleted');
        });
    }

    protected function logAction($action)
    {
        $oldValues = null;
        $newValues = $this->getAttributes();

        if ($action === 'updated') {
            $oldValues = array_intersect_key($this->getOriginal(), $this->getDirty());
            $newValues = $this->getDirty();
            
            // Don't log if only timestamps changed
            if (empty($newValues) || (count($newValues) === 2 && isset($newValues['updated_at']))) {
                return;
            }
        }

        if ($action === 'deleted') {
            $oldValues = $this->getAttributes();
            $newValues = null;
        }

        // Hide sensitive fields
        $hidden = ['password', 'remember_token'];
        if ($oldValues) {
            foreach ($hidden as $field) unset($oldValues[$field]);
        }
        if ($newValues) {
            foreach ($hidden as $field) unset($newValues[$field]);
        }

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
