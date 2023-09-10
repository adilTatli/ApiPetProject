<?php

namespace App\Services\Localization;

use Illuminate\Support\Facades\App;

class Localization
{
    /**
     * Устанавливает текущую локаль приложения на основе сегмента URL-адреса.
     *
     * Этот метод получает сегмент URL-адреса и проверяет, является ли он допустимой
     * локалью, которая указана в конфигурации приложения. Если сегмент URL-адреса
     * соответствует допустимой локали, то она устанавливается как текущая локаль
     * для приложения, и метод возвращает эту локаль.
     *
     * @return string|null Текущая локаль или null, если локаль не найдена.
     */
    public function locale()
    {
        $locale = request()->segment(1);
        if ($locale && in_array($locale, config('app.locales'))) {
            App::setLocale($locale);
            return $locale;
        }
    }
}
