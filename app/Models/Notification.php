<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'title',
        'message',
        'time',
        'read',
        'user_id',
        'assignee_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'time' => 'timestamp',
            'read' => 'boolean',
        ];
    }

    public static function addNotification(  $message, $assignee_id = null,$title='Notification' ,$type='info'){
        self::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'time' => now(),
            'read' => false,
            'user_id' => auth()->id(),
            'assignee_id' => $assignee_id,
        ]);
    }
}
