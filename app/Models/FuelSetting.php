<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FuelSetting extends Model
{
    use HasFactory;

    protected $table = 'fuel_settings';

    protected $fillable = [
      'employee_id',
      'fuel_type',
      'start_inv',
      'import_loss_rate',
      'export_loss_rate',
    ];
}
