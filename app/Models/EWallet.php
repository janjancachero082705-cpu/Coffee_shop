<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EWallet extends Model {
    protected $fillable = ['name', 'code', 'account_name', 'account_number', 'qr_code', 'logo_path', 'is_active'];
}