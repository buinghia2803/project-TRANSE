<?php

namespace App\Services\GMO;

class GMOHelper
{
    /**
     * Generate an unique order ID
     *
     * @return string
     */
    public static function generateOrderID(): string
    {
        $id = uniqid();

        return "trademark-" . $id;
    }

    /**
     * Is error response
     *
     * @param   string $response
     * @return  boolean
     */
    public static function isError(string $response): bool
    {
        if (str_contains($response, 'ErrCode') || str_contains($response, 'errCode')) {
            return true;
        }

        return false;
    }

    /**
     * Convert response of api idPass to Array
     *
     * @param   string $response
     * @return  array
     */
    public static function convertResponseIdPassToArray(string $response): array
    {
        $result = [];
        $response = explode('&', $response);

        foreach ($response as $data) {
            $arrData = explode('=', $data);
            $key = $arrData[0];
            $result[$key] = $arrData[1];
        }

        return $result;
    }
}
