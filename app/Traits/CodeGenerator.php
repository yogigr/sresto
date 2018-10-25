<?php 

namespace App\Traits;

use DB;
use Carbon\Carbon;

trait CodeGenerator {

	public function getCode($char, $table, $digitLength = 3)
	{
		$maxCode = DB::table($table)->max('code');
        if (is_null($maxCode)) {
        	return $char . str_pad(1, $digitLength, '0', STR_PAD_LEFT);
        } else {
            $currentNumber = substr($maxCode, strlen($char), $digitLength);
            $newNumber = str_pad((int)($currentNumber + 1), $digitLength, '0', STR_PAD_LEFT);
            return $char . $newNumber;
        }
	}

	public function getCodeWithDatetime($char, $table, $digitLength = 3)
    {
        $maxCodeToday = DB::table($table)->whereDate('created_at', today())->max('code');
        $today = Carbon::parse(today())->format('Ymd');
        if (is_null($maxCodeToday)) {
        	return $char . $today . str_pad(1, $digitLength, '0', STR_PAD_LEFT);
        } else {
            $currentNumber = substr($maxCodeToday, strlen($char) + strlen($today), $digitLength);
            $newNumber = str_pad((int)($currentNumber + 1), $digitLength, '0', STR_PAD_LEFT);
            return $char . $today . $newNumber;
        }
    }
}