<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyPumpLog extends Model
{
    use HasFactory;

    protected $table = 'daily_pump_logs';
    
    protected $fillable = [
        'log_date', 'employee_id', 'pump_id', 'lit', 'money', 'millis', 'total_f3', 'fuel_type'
    ];

    public $timestamps = false;
}
