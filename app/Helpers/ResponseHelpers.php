<?php

namespace App\Helpers;

class ResponseHelpers
{
    public static function jsonResponse($content, $status = 200, $headers = [])
    {
        return response($content, $status, array_merge($headers, ['Content-Type' => 'application/json']));
    }

    public static function emptyResponse()
    {
        return response(null, 204);
    }

    private function __construct()
    { }
}
