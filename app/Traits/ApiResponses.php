<?php

namespace App\Traits;

trait ApiResponses
{
    protected function success($message, $statusCode = 200)
    {
        return response()->json(
            [
                "message" => $message,
                "status" => $statusCode,
            ],
            $statusCode
        );
    }

    protected function ok($message)
    {
        return $this->success($message);
    }
}
