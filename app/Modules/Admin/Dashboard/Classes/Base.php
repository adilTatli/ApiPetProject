<?php

namespace App\Modules\Admin\Dashboard\Classes;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Menu;

use App\Modules\Admin\Menu\Models\Menu as MenuModel;

class Base extends Controller
{
    protected $template;
    protected $user;
    protected $title;
    protected $content;
    protected $sidebar;
    protected $vars;
    protected $locale;
    protected $service;

    public function __construct()
    {
        $this->template = "Admin::Dashboard.dashboard";

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->locale = App::getLocale();
            return $next($request);
        });
    }

    /**
     * Отображает выходную HTML-страницу с использованием шаблона и данных.
     *
     * Этот метод формирует содержимое страницы, включая основной контент и боковую панель,
     * используя указанный шаблон и данные. Он также устанавливает данные для основного
     * контента и боковой панели и возвращает итоговое представление страницы.
     *
     * @return \Illuminate\View\View Отображаемое представление страницы.
     */
    protected function renderOutput()
    {
        $this->vars = Arr::add($this->vars, 'content', $this->content);

        $menu = $this->getMenu();

        $this->sidebar = view('Admin::layouts.parts.sidebar')->with([
            'menu' => $menu,
            'user' => $this->user
        ])->render();
        $this->vars = Arr::add($this->vars, 'sidebar', $this->sidebar);
        return view($this->template)->with($this->vars);
    }

    private function getMenu()
    {
        return Menu::make('menuRenderer', function ($m) {
            foreach (MenuModel::MenuByType(MenuModel::MENU_TYPE_ADMIN)->get() as $item) {
                $path = $item->path;
                if ($path && $this->checkRoute($path)) {
                    $path = route($path);
                }

                if ($item->parent == 0) {
                    $m->add($item->title, $path)->id($item->id)->data('permissions', []);
                } else {
                    if ($m->find($item->parent)) {
                        $m->find($item->parent)->add($item->title, $path)->id($item->id)->data('permissions', []);
                    }
                }
            }
        })->filter(function ($item) {
            return true;
        });
    }

    private function checkRoute($path)
    {
        $routes = \Route::getRoutes()->getRoutes();

        foreach ($routes as $route) {
            if ($route->getName() == $path) {
                return true;
            }
        }

        return false;
    }
}
