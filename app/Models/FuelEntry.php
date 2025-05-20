<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'entry_time',
        'employee_id',
        'fuel_type',
        'unit_type',
        'price',
        'vat_percentage',
        'quantity',
        'document_code',
    ];
    
    protected $casts = [
    'entry_time' => 'datetime',
    ];
  
}
