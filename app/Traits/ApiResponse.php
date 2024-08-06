<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    /**
     * @param string $message
     * @param int $status
     * @param array|null $data
     * @return JsonResponse
     */
    protected function response(string $message, int $status = Response::HTTP_OK, array $data = null): JsonResponse
    {
        $array = [
            'message' => $message,
            'status' => in_array($status, $this->statuses()),
            'code' => $status,
            'data' => $data,
        ];
        return response()->json($array, $status);
    }

    protected function statuses(): array
    {
        return [
            Response::HTTP_OK,
            Response::HTTP_CREATED,
            Response::HTTP_ACCEPTED,
            Response::HTTP_NO_CONTENT,
        ];
    }

    protected function notFoundResponse(string $message, int $code = Response::HTTP_NOT_FOUND): JsonResponse
    {
        return $this->response(
            $message,
            $code,
            null
        );
    }
}
