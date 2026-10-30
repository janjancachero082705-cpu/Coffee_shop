<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 
        'module', 
        'action', 
        'description', 
        'quantity', 
        'read_at', 
        'reference_id', 
        'reference_type'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public static function record($module, $action, $description, $reference = null, $quantity = null)
    {
        self::create([
            'user_id' => auth()->id() ?? 1,
            'module' => $module,
            'action' => $action,
            'description' => $description,
            'quantity' => $quantity,
            'reference_id' => is_object($reference) ? $reference->id : null,
            'reference_type' => is_object($reference) ? get_class($reference) : null,
        ]);
    }
}