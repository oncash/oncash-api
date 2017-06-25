<?php

namespace OnCash\Exception;


class ApiException extends \Exception
{
    /**
     * @param $status_code
     * @return string
     */
    public static function getExceptionFromStatusCode($status_code)
    {
        //TODO: Add more status codes
        switch($status_code) {
            case 404:
                return "NotFoundException";
            default:
                return "ApiException";
        }
    }
}