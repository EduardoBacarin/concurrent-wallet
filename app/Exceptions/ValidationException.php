<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;

class ValidationException extends Exception
{
    protected $validator;
    protected $code = 422;

    public function __construct(Validator $validator) {
        $this->validator = $validator;
    }

    public function render() {
        return response()->json([
            "success" => false,
            "code" => 422,
            "message" => $this->validator->errors()->first()
        ], $this->code);
    }
}
