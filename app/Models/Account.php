<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Account extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code', 'type', 'parent_id'];

    use LogsActivity;

    protected static $logName = 'account';

    protected static $logAttributes = ['*']; // log semua field
    protected static $logOnlyDirty = true;   // hanya log data yang berubah
    protected static $submitEmptyLogs = false;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('account')
            ->logAll() // log semua kolom yang diubah
            ->logOnlyDirty() // hanya log data yang berubah
            ->setDescriptionForEvent(fn(string $eventName) => "Account was {$eventName}");
    }
    
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function journalDetails()
    {
        return $this->hasMany(JournalDetail::class, 'account_id');
    }
}
