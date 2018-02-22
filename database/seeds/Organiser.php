<?php

use Illuminate\Database\Seeder;

class Organiser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        

    	Schema::disableForeignKeyConstraints();
        DB::table('organisers')->delete();
        Schema::enableForeignKeyConstraints();

        $organiser = [

        	[
        		'id' => '1',
        		'account_id' => '1',
        		'name' => 'You',
        		'about' => 'First Organiser',
        		'email' => 'email@domain.com',
        		'phone' => '07777777',
        		'confirmation_key' => str_random(15),

        	],
        ];







       DB::table('organisers')->insert($organiser);

    }
}
