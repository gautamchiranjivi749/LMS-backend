<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogger
{
    public static function log(
        $action,
        $module,
        $moduleId = null,
        $description = null
    ) {

        ActivityLog::create([

            'user_id' => auth()->id(),

            'action' => $action,

            'module' => $module,

            'module_id' => $moduleId,

            'description' => $description,

            'ip_address' => request()->ip(),

            'user_agent' => request()->userAgent(),

        ]);

    }
}