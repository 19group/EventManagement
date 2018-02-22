<?php

use Illuminate\Database\Seeder;

class coupon extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('coupons')->delete();
        Schema::enableForeignKeyConstraints();

        $couponz = [

        	[
        		'id' => '1',
        		'coupon_code' => 'TZ82992FK',
        		'state' => 'valid',
        		'discount' => '25',
        		'user' => 'username',
        		'ticket' => 'Early Bird Gold',
        		'event_id' => '1',

        	],
        ];







       DB::table('coupons')->insert($couponz);
    }
}
