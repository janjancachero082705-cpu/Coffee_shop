<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = ['gcash_name', 'gcash_number', 'gcash_qr_path'];
}