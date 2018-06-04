<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

/*
  Attendize.com   - Event Management & Ticketing
 */

class Payment extends \Illuminate\Database\Eloquent\Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array $fillable
     */
    protected $fillable = [
        'event_id',
        'full_name',
        'payer_email',
        'receiver_email',
        'payer_status',
        'payment_status',
        'amount',
        'currency',
        'payment_date',
        'txn_id',
        'custom',
        'bought_tickets',
        'order_details',
        'paypal_verified',
        'order_completed',
        'transaction_approved',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @return array $dates
     */
    //public function getDates()
    //{
    //    return ['created_at', 'updated_at'];
    //}
}
