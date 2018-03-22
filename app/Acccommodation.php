<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Acccommodation extends Model
{
    protected $fillable = ['title', 'full_name', 'email', 'price', 'days', 'date', 'hotel_status', 'time', 'amount'];
}
