<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['title', 'full_name','payer_email','receiver_email','payer_status','payment_status','amount','currency','payment_date','txn_id','custom'];
}
