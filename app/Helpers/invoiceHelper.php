<?php

namespace App\Helpers;

// use Exception;
use Illuminate\Support\Facades\DB;

class invoiceHelper {


    public static function generateInvoiceNumber($prefix, $length) {
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
        $exists = DB::table('invoices')->where('invoice_no', $uniqueCod)->exists();
        if ($exists) {
            return self::generateInvoiceNumber($prefix, $length);
        }
        return $uniqueCod;
    }
}