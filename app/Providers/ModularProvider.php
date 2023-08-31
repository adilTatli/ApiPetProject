<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ModularProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $modules = config('modular.modules');
        $path = config('modular.path');

        if ($modules) {
            Route::group([
               'prefix' => ''
            ], function() use($modules, $path) {

                foreach ($modules as $mod => $submodules) {
                    foreach ($submodules as $key => $sub) {

                        $relativePath = "/$mod/$sub";

                        Route::middleware('web')
                            ->group(function () use($mod, $sub, $relativePath, $path) {
                                $this->getWebRoutes($mod, $sub, $relativePath, $path);
                            });

                        Route::prefix('api')
                            ->middleware('api')
                            ->group(function () use($mod, $sub, $relativePath, $path) {
                                $this->getApiRoutes($mod, $sub, $relativePath, $path);
                            });
                    }
                }

            });
        }

        $this->app['view']->addNamespace('Pub', base_path().'/resources/views/Pub');
        $this->app['view']->addNamespace('Admin', base_path().'/resources/views/Admin');
    }

    /**
     * Получает веб-маршруты для указанного модуля и подмодуля.
     *
     * Этот метод получает веб-маршруты из файла web.php для указанного модуля и подмодуля.
     * Он проверяет наличие файла маршрутов и, в зависимости от настроек модуля, создает
     * группу маршрутов с префиксом и middleware или добавляет маршруты без группы.
     *
     * @param string $mod Имя модуля.
     * @param string $sub Имя подмодуля.
     * @param string $relativePath Относительный путь к папке с модулем и подмодулем.
     * @param string $path Абсолютный путь к папке с модулем.
     * @return void
     */
    private function getWebRoutes($mod, $sub, $relativePath, $path)
    {
        $routesPath = $path.$relativePath.'/Routes/web.php';
        if (file_exists($routesPath)) {

            if ($mod != config('modular.groupWithoutPrefix')) {
                Route::group(
                    [
                        'prefix' => strtolower($mod),
                        'middleware' => $this->getMiddleware($mod)
                    ],
                    function () use($mod, $sub, $routesPath) {
                        Route::namespace("App\Modules\\$mod\\$sub\Controllers")
                            ->group($routesPath);
                    }
                );
            } else {
                Route::namespace("App\Modules\\$mod\\$sub\Controllers")->
                    middleware($this->getMiddleware($mod))->
                    group($routesPath);
            }

        }
    }

    /**
     * Получает API-маршруты для указанного модуля и подмодуля.
     *
     * Этот метод получает API-маршруты из файла api.php для указанного модуля и подмодуля.
     * Он проверяет наличие файла маршрутов и создает группу маршрутов с префиксом и middleware.
     *
     * @param string $mod Имя модуля.
     * @param string $sub Имя подмодуля.
     * @param string $relativePath Относительный путь к папке с модулем и подмодулем.
     * @param string $path Абсолютный путь к папке с модулем.
     * @return void
     */
    private function getApiRoutes($mod, $sub, $relativePath, $path)
    {
        $routesPath = $path.$relativePath.'/Routes/api.php';
        if (file_exists($routesPath)) {
            Route::group(
                [
                    'prefix' => strtolower($mod),
                    'middleware' => $this->getMiddleware($mod, 'api')
                ],
                function () use($mod, $sub, $routesPath) {
                    Route::namespace("App\Modules\\$mod\\$sub\Controllers")->
                        group($routesPath);
                }
            );
        }
    }

    /**
     * Получает middleware для указанного модуля и ключа.
     *
     * Этот метод получает middleware для указанного модуля и ключа (web или api) из конфигурации.
     * Он собирает middleware из конфигурации для соответствующего модуля и ключа.
     *
     * @param string $mod Имя модуля.
     * @param string $key Ключ для поиска middleware.
     * @return array Массив middleware для указанного модуля и ключа.
     */
    private function getMiddleware($mod, $key = 'web')
    {
        $middleware = [];

        $config = config('modular.groupMiddleware');

        if (isset($config[$mod])) {
            if (array_key_exists($key, $config[$mod])) {
                $middleware = array_merge($middleware, $config[$mod][$key]);
            }
        }

        return $middleware;
    }
}
