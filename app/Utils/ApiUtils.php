<?php

namespace App\Utils;

use Exception;

class ApiUtils
{
    public static function validateJson($request): void
    {
        $data = $request->getContent();

        json_decode($data);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON request. Please check the request content.');
        }
    }
}
