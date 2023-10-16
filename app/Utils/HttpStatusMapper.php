<?php

namespace App\Utils;

class HttpStatusMapper
{
    public static function getStatusCode(string $status): int
    {
        // Receive a string and return the corresponding HTTP status code
        $statusCodes = [
            "SUCCESS" => 200,
            "CREATED" => 201,
            "ACCEPTED" => 202,
            "NO_CONTENT" => 204,
            "BAD_REQUEST" => 400,
            "UNAUTHORIED" => 401,
            "NOT_FOUND" => 404,
            "INTERNAL_SERVER_ERROR" => 500,
        ];

        if (!array_key_exists($status, $statusCodes)) {
            throw new \Exception("Invalid status code");
        }
        return $statusCodes[$status];
    }
}
