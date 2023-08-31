<?php

namespace App\Modules\Pub\Auth\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\Pub\Auth\Requests\LoginRequest;
use App\Services\Response\ResponseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Обрабатывает запрос на вход пользователя в систему.
     *
     * Этот метод обрабатывает запрос на вход пользователя в систему, используя переданный
     * экземпляр класса LoginRequest для валидации данных. Если данные аутентификации не
     * совпадают, метод возвращает JSON-ответ с сообщением об ошибке. В противном случае,
     * создается токен доступа и возвращается JSON-ответ с данными о пользователе и токене.
     *
     * @param \App\Http\Requests\LoginRequest $request Экземпляр класса LoginRequest.
     * @return \Illuminate\Http\JsonResponse JSON-ответ после аутентификации.
     */
    public function login(LoginRequest $request)
    {
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return ResponseService::sendJsonResponse(
                false,
                403,
                ['message' => __('auth.login_error')],
            );
        }

        $user = $request->user();

        $tokenResult = $user->createToken('Personal Access Token');

        return ResponseService::sendJsonResponse(
            true,
            200,
            [],
            [
                'api_token' => $tokenResult->accessToken,
                'user' => $user,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            ],
        );
    }
}
