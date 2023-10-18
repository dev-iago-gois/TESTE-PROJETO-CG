<?php

namespace App\Http\Utils;

class StatusChecker
{
    public static function checkStatus(int $saleId, string $status): void
    {
        $validStatus = ['pending'];

        if ($status != 'pending') {
            throw new \Exception("Sale ID {$saleId} cannot be updated");
        }
    }
}
