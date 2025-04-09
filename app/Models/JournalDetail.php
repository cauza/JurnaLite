<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class JournalDetail extends Model
{
    use HasFactory;
    protected $fillable = ['journal_entry_id', 'account_id', 'debit', 'credit'];

    use LogsActivity;

    // Optional: agar log lebih informatif
    protected static $logName = 'journal_detail';
    protected static $logAttributes = ['journal_entry_id', 'account_id', 'debit', 'credit', 'description'];
    protected static $logOnlyDirty = true; // hanya log perubahan data

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(self::$logAttributes)
            ->useLogName(self::$logName)
            ->setDescriptionForEvent(function (string $eventName) {
                return "Data JournalDetail telah di {$eventName}";
            });
    }
    
    public function account()
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    public function journalEntry()
    {
        return $this->belongsTo(\App\Models\JournalEntry::class);
    }

}
