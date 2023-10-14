<?php

namespace App\Utils;

class HttpStatusMapper
{
    public static function getStatusCode(string $status): int
    {
        $statusCodes = [
            "SUCCESS" => 200,
            "CREATED" => 201,
            "BAD_REQUEST" => 400,
            "UNAUTHORIED" => 401,
            "NOT_FOUND" => 404,
            "internal_server_error" => 500,
        ];

        if (!array_key_exists($status, $statusCodes)) {
            throw new \Exception("Invalid status code");
        }
        return $statusCodes[$status];
    }
}
