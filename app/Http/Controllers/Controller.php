<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function responsePattern($code, $success, $message, $data = null){
        $response = [
            "success" => $success,
            "code" => $code,
            "message" => $message
        ];
        if ($data) $response['data'] = $data;
        return response()->json($response, $code);
    }
}
