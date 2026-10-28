<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = ['user_id', 'module', 'action', 'description', 'read_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function record($module, $action, $description)
    {
        self::create([
            'user_id' => auth()->id() ?? 1,
            'module' => $module,
            'action' => $action,
            'description' => $description
        ]);
    }
}