<?php

namespace app\helpers;

/**
 *
 */
class ResponseHelper
{
    /**
     * @param string $message
     * @param array $errors
     * @return array
     */
    public static function error(string $message, array $errors = [], int $status = 400): array
    {
        $error =  [
            'status' => $status,
            'message' => $message
        ];

        if (!empty($errors)) {
            $error['errors'] = $errors;
        }

        return $error;
    }

    /**
     * @param array $response
     * @param string $message
     * @param int $status
     * @return array
     */
    public static function success(array $response = [], int $status = 200): array
    {
        $success = [
            'status' => $status
        ];

        if (!empty($response)) {
            $success['response'] = $response;
        }

        return $success;
    }
}