<?php

namespace App\Helpers;

// use Exception;
use Illuminate\Support\Facades\DB;

class appHelper {


    public static function generateQuotationNumber($prefix, $length) {
        if (function_exists("radom_bytes")) {
            $bytes = random_bytes(ceil($length /2));
        }
        elseif (function_exists("openssl_random_pseudo_bytes")){
            $bytes = openssl_random_pseudo_bytes(ceil($length /2));
        }
        else {
            throw new \Exception("no cryptographically secure random function available");
        }

        $uniqueCod = $prefix . strtoupper(substr(bin2hex($bytes), 0, $length));
        $exists = DB::table('quotations')->where('quotation_no', $uniqueCod)->exists();
        if ($exists) {
            return self::generateQuotationNumber($prefix, $length);
        }
        return $uniqueCod;
    }
}