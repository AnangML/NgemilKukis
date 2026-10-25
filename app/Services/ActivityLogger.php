<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ActivityLogger
{
    /**
     * Record a new activity log entry.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function log(
        string $action,
        string $category,
        string $description,
        ?Model $subject = null,
        ?array $properties = null,
        ?User $user = null
    ): ?ActivityLog {
        try {
            $currentUser = $user ?? Auth::user();

            return ActivityLog::query()->create([
                'user_id' => $currentUser?->id,
                'user_name' => $currentUser?->name ?? 'Tamu (Guest)',
                'user_role' => $currentUser?->role ?? 'guest',
                'action' => $action,
                'category' => $category,
                'description' => $description,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject?->getKey(),
                'properties' => $properties,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Fail-safe: logging failure must not break customer transactions or user requests
            Log::warning('Failed to write activity log: '.$e->getMessage(), [
                'action' => $action,
                'category' => $category,
            ]);

            return null;
        }
    }

    /**
     * Log an authentication activity.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function auth(string $action, string $description, ?User $user = null, ?array $properties = null): ?ActivityLog
    {
        return self::log($action, 'auth', $description, $user, $properties, $user);
    }

    /**
     * Log an order lifecycle activity.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function order(string $action, string $description, ?Model $order = null, ?array $properties = null, ?User $user = null): ?ActivityLog
    {
        return self::log($action, 'order', $description, $order, $properties, $user);
    }

    /**
     * Log a catalog or product activity.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function catalog(string $action, string $description, ?Model $item = null, ?array $properties = null, ?User $user = null): ?ActivityLog
    {
        return self::log($action, 'catalog', $description, $item, $properties, $user);
    }

    /**
     * Log a settings or brand change activity.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function settings(string $action, string $description, ?array $properties = null, ?User $user = null): ?ActivityLog
    {
        return self::log($action, 'settings', $description, null, $properties, $user);
    }

    /**
     * Log a staff management activity.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function staff(string $action, string $description, ?Model $targetUser = null, ?array $properties = null, ?User $user = null): ?ActivityLog
    {
        return self::log($action, 'staff', $description, $targetUser, $properties, $user);
    }
}
