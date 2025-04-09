<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class JournalEntry extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'description', 'reference', 'user_id'];

    use LogsActivity;

    protected static $logName = 'journal_entry';

    protected static $logAttributes = ['*']; // log semua field
    protected static $logOnlyDirty = true;   // hanya log data yang berubah
    protected static $submitEmptyLogs = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('journal_entry')
            ->logAll() // log semua kolom yang diubah
            ->logOnlyDirty() // hanya log data yang berubah
            ->setDescriptionForEvent(fn(string $eventName) => "Journal Entry was {$eventName}");
    }
    
    public function details()
    {
        return $this->hasMany(JournalDetail::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
