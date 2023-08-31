<?php

namespace App\Services\Response;

class ResponseService
{
    /**
     * Генерирует параметры ответа для API.
     *
     * Этот статический метод создает и возвращает массив параметров ответа для использования в API.
     * Параметры ответа включают статус ответа, список ошибок (при наличии) и данные (при наличии).
     *
     * @param string $status Статус ответа.
     * @param array $errors Массив ошибок.
     * @param array $data Массив данных.
     * @return array Массив параметров ответа.
     */
    private static function ResponseParams($status, $errors = [], $data = [])
    {
        return [
            'status' => $status,
            'errors' => (object)$errors,
            'data' => (object)$data,
        ];
    }

    /**
     * Отправляет JSON-ответ.
     *
     * Этот статический метод отправляет JSON-ответ в соответствии с переданными параметрами.
     * Ответ будет иметь указанный статус, HTTP-код, список ошибок (при наличии) и данные (при наличии).
     *
     * @param string $status Статус ответа.
     * @param int $code HTTP-код ответа.
     * @param array $errors Массив ошибок.
     * @param array $data Массив данных.
     * @return \Illuminate\Http\JsonResponse JSON-ответ.
     */
    public static function sendJsonResponse($status, $code = 200, $errors = [], $data = [])
    {
        return response()->json(
          self::ResponseParams($status, $errors, $data),
            $code
        );
    }

    /**
     * Генерирует успешный JSON-ответ.
     *
     * Этот статический метод генерирует JSON-ответ с успешным статусом (true) и HTTP-кодом 200.
     * При желании, можно также передать дополнительные данные для включения в ответ.
     *
     * @param array $data Массив данных для ответа.
     * @return \Illuminate\Http\JsonResponse JSON-ответ с успешным статусом.
     */
    public static function success($data = [])
    {
        return self::sendJsonResponse(true, 200, [], $data);
    }

    /**
     * Генерирует JSON-ответ об отсутствии найденных данных (HTTP 404 Not Found).
     *
     * Этот статический метод генерирует JSON-ответ с статусом "false", HTTP-кодом 404 (Not Found)
     * и без ошибок или данных.
     *
     * @return \Illuminate\Http\JsonResponse JSON-ответ о не найденных данных.
     */
    public static function notFound()
    {
        return self::sendJsonResponse(false, 404, [], []);
    }
}
