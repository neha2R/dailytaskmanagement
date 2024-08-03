<?php
namespace App;

class ApiResponse
{
    public static function makeResponse($data, $message, $code)
    {
        return response()->json([
            'status' => $code < 400,
            'data' => $data,
            'message' => $message,
        ], $code);
    }
    
    public static function validationError($errors, $message = 'Validation Error')
    {
        return self::makeResponse($errors, $message, 422);
    }

    public static function success($data, $message = 'Success', $code = 200)
    {
        return self::makeResponse($data, $message, $code);
    }

    public static function created($data, $message = 'Resource created', $code = 201)
    {
        return self::makeResponse($data, $message, $code);
    }

    public static function noContent($message = 'No content', $code = 204)
    {
        return self::makeResponse(null, $message, $code);
    }

    public static function badRequest($message = 'Bad request', $code = 400)
    {
        return self::makeResponse(null, $message, $code);
    }

    public static function unauthorized($message = 'Unauthorized', $code = 401)
    {
        return self::makeResponse(null, $message, $code);
    }

    public static function forbidden($message = 'Forbidden', $code = 403)
    {
        return self::makeResponse(null, $message, $code);
    }

    public static function notFound($message = 'Not Found', $code = 404)
    {
        return self::makeResponse(null, $message, $code);
    }
    // send with blank array
    public static function noDataFound($data,$message = 'No Content Found', $code = 204)
    {
        return self::makeResponse($data, $message, $code);
    }

    public static function internalServerError($error=null , $message = 'Internal Server Error', $code = 500)
    {
        return self::makeResponse($error, $message, $code);
    }
}
