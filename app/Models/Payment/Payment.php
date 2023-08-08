<?php

namespace App\Models\Payment;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;

    use HasFactory;

    protected $fillable = ['user_id', 'transaction_id', 'amount', 'store_amount', 'val_id', 'card_type',
        'card_issuer', 'card_brand', 'status', 'currency', 'bank_transaction_id', 'card_no', 'card_sub_brand',
        'transaction_date','store_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
