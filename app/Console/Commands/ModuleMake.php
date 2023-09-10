<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class ModuleMake extends Command
{

    private $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:module {name}
                                                  {--all}
                                                  {--migration}
                                                  {--vue}
                                                  {--view}
                                                  {--controller}
                                                  {--model}
                                                  {--api}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct();

        $this->files = $filesystem;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->input->setOption('migration', true);
            $this->input->setOption('vue', true);
            $this->input->setOption('view', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('model', true);
            $this->input->setOption('api', true);
        }

        if ($this->option('model')) {
            $this->createModel();
        }

        if ($this->option('controller')) {
            $this->createController();
        }

        if ($this->option('api')) {
            $this->createApiController();
        }

        if ($this->option('migration')) {
            $this->createMigration();
        }

        if ($this->option('vue')) {
            $this->createVueComponent();
        }

        if ($this->option('view')) {
            $this->createView();
        }
    }

    /**
     * Создает новый класс модели на основе переданного аргумента имени.
     *
     * Этот метод генерирует и вызывает команду Artisan 'make:model', чтобы создать новый класс модели
     * в определенном пространстве имен модуля и директории моделей.
     *
     * @return void
     */
    private function createModel()
    {
        $model = Str::singular(Str::studly(class_basename($this->argument('name'))));

        $this->call('make:model', [
            'name' => "App\\Modules\\".trim($this->argument('name'))."\\Models\\".$model,
        ]);
    }

    /**
     * Создает новый класс контроллера на основе переданного аргумента имени.
     *
     * Этот метод генерирует новый класс контроллера и связанные с ним маршруты. Он использует заготовку
     * шаблона для замены заполнителей реальной информацией о классе и пространстве имен, после чего
     * записывает получившийся контент в соответствующее место файла.
     *
     * @return void
     */
    private function createController()
    {
        // Извлекаем имена контроллера и модели
        $controller = Str::studly(class_basename($this->argument('name')));
        $modelName = Str::singular(Str::studly(class_basename($this->argument('name'))));

        // Получаем путь к файлу контроллера
        $path = $this->getControllerPath($this->argument('name'));

        if ($this->alreadyExists($path)) {
            $this->error('Controller already exists!');
        } else {
            // Создаем директорию, если она еще не существует
            $this->makeDirectory($path);

            // Загружаем заготовку шаблона контроллера
            $stub = $this->files->get(base_path('resources/stubs/controller.model.api.stub'));

            // Заменяем заполнители на реальные значения
            $stub = str_replace(
                [
                    'DummyNamespace',
                    'DummyRootNamespace',
                    'DummyClass',
                    'DummyFullModelClass',
                    'DummyModelClass',
                    'DummyModelVariable',
                ],
                [
                    "App\\Modules\\".trim($this->argument('name'))."\\Controllers",
                    $this->laravel->getNamespace(),
                    $controller.'Controller',
                    "App\\Modules\\".trim($this->argument('name'))."\\Models\\{$modelName}",
                    $modelName,
                    lcfirst(($modelName))
                ],
                $stub
            );

            // Записываем измененную заготовку в файл
            $this->files->put($path, $stub);
            $this->info('Controller created sucessfully.');
        }

        $this->updateModularConfig();

        // Создаем связанные маршруты
        $this->createRoutes($controller, $modelName);
    }

    /**
     * Обновляет конфигурационный файл modular.php с информацией о модуле.
     *
     * Этот метод обновляет конфигурационный файл modular.php, добавляя информацию
     * о новом модуле в соответствующую группу. Метод анализирует существующую
     * конфигурацию и добавляет имя модуля в группу, если его там еще нет.
     *
     * @return void
     */
    private function updateModularConfig()
    {
        // Получаем имя группы из аргумента команды (первая часть пространства имен).
        $group = explode('\\', $this->argument('name'))[0];

        // Получаем имя модуля из аргумента команды.
        $module = Str::studly(class_basename($this->argument('name')));

        // Получаем содержимое файла конфигурации modular.php.
        $modular = $this->files->get(base_path('config/modular.php'));

        $matches = [];

        // Используем регулярное выражение для поиска соответствующей группы модулей.
        preg_match("/'modules' => \[.*?'{$group}' => \[(.*?)\]/s", $modular, $matches);

        // Если найденная группа существует, проверяем наличие имени модуля в ней.
        if (count($matches) == 2) {
            if (!preg_match("/'{$module}'/", $matches[1])) {

                // Разделяем содержимое файла перед и после найденной группы.
                $parts = preg_split("/('modules' => \[.*?'{$group}' => \[)/s", $modular, 2, PREG_SPLIT_DELIM_CAPTURE);

                // Если удалось разделить на три части, обновляем конфигурацию.
                if (count($parts) == 3) {

                    // Создаем новую строку конфигурации с добавленным именем модуля.
                    $configStr = $parts[0].$parts[1]."\n            '$module',".$parts[2];

                    // Записываем обновленную конфигурацию обратно в файл.
                    $this->files->put(base_path('config/modular.php'), $configStr);
                }
            }
        }
    }

    /**
     * Создает новый класс контроллера API на основе переданного аргумента имени.
     *
     * Этот метод генерирует новый класс контроллера API и связанные с ним маршруты. Он использует заготовку
     * шаблона для замены заполнителей реальной информацией о классе и пространстве имен, после чего
     * записывает получившийся контент в соответствующее место файла.
     *
     * @return void
     */
    private function createApiController()
    {
        // Извлекаем имена контроллера и модели
        $controller = Str::studly(class_basename($this->argument('name')));
        $modelName = Str::singular(Str::studly(class_basename($this->argument('name'))));

        // Получаем путь к файлу контроллера API
        $path = $this->getApiControllerPath($this->argument('name'));

        if ($this->alreadyExists($path)) {
            $this->error('Controller already exists!');
        } else {
            // Создаем директорию, если она еще не существует
            $this->makeDirectory($path);

            // Загружаем заготовку шаблона контроллера API
            $stub = $this->files->get(base_path('resources/stubs/controller.model.api.stub'));

            // Заменяем заполнители на реальные значения
            $stub = str_replace(
                [
                    'DummyNamespace',
                    'DummyRootNamespace',
                    'DummyClass',
                    'DummyFullModelClass',
                    'DummyModelClass',
                    'DummyModelVariable',
                ],
                [
                    "App\\Modules\\".trim($this->argument('name'))."\\Controllers\\Api",
                    $this->laravel->getNamespace(),
                    $controller.'Controller',
                    "App\\Modules\\".trim($this->argument('name'))."\\Models\\{$modelName}",
                    $modelName,
                    lcfirst(($modelName))
                ],
                $stub
            );

            // Записываем измененную заготовку в файл
            $this->files->put($path, $stub);
            $this->info('Controller created successfully!');
        }

        $this->updateModularConfig();

        // Создаем связанные маршруты для API
        $this->createApiRoutes($controller, $modelName);
    }

    /**
     * Создает новую миграцию для таблицы на основе переданного аргумента имени.
     *
     * Этот метод генерирует новую миграцию для таблицы и указанной таблицы и связанными с ней атрибутами.
     * Он вызывает команду 'make:migration' Artisan и использует аргументы для определения имени миграции
     * и пути, где она должна быть создана.
     *
     * @return void
     */
    private function createMigration()
    {
        // Извлекаем имя таблицы из аргумента имени
        $table = Str::plural(Str::snake(class_basename($this->argument('name'))));

        try {
            // Вызываем команду 'make:migration' Artisan с нужными аргументами
            $this->call('make:migration', [
                'name' => "create_{$table}_table",
                '--create' => $table,
                '--path' => "App\\Modules\\".trim($this->argument('name'))."\\Migrations",
            ]);
        }
        catch (\Exception $e) {
            // Обрабатываем возможные ошибки и выводим сообщение
            $this->error($e->getMessage());
        }
    }

    /**
     * Создает новый Vue-компонент на основе переданного аргумента имени.
     *
     * Этот метод генерирует новый файл Vue-компонента и использует шаблон для замены имени класса.
     * Затем получившийся контент записывается в соответствующее место файла.
     *
     * @return void
     */
    private function createVueComponent()
    {
        // Получаем путь к файлу Vue-компонента
        $path = $this->getVueComponentPath($this->argument('name'));

        // Получаем имя компонента из аргумента имени
        $component = Str::studly(class_basename($this->argument('name')));

        if ($this->alreadyExists($path)) {
            $this->error('Vue Component already exists!');
        } else {
            // Создаем директорию, если она еще не существует
            $this->makeDirectory($path);

            // Загружаем шаблон файла Vue-компонента
            $stub = $this->files->get(base_path('resources/stubs/vue.component.stub'));

            // Заменяем заполнитель на имя класса компонента
            $stub = str_replace(
                [
                    'DummyClass',
                ],
                [
                    $component,
                ],
                $stub
            );

            // Записываем измененный шаблон в файл
            $this->files->put($path, $stub);
            $this->info('Vue Component created successfully.');
        }
    }

    /**
     * Создает новое представление (view) на основе переданного аргумента имени.
     *
     * Этот метод генерирует новый файл представления (view) и использует шаблон для создания содержимого.
     * Затем полученное содержимое записывается в соответствующий файл.
     *
     * @return void
     */
    private function createView()
    {
        // Получаем пути к файлам представлений
        $paths = $this->getViewPath($this->argument('name'));

        foreach ($paths as $path) {
            // Получаем имя представления из аргумента имени
            $view = Str::studly(class_basename($this->argument('name')));

            if ($this->alreadyExists($path)) {
                $this->error('View already exists!');
            } else {
                // Создаем директорию, если она еще не существует
                $this->makeDirectory($path);

                // Загружаем шаблон файла представления
                $stub = $this->files->get(base_path('resources/stubs/view.stub'));

                // Необходимые замены, если такие есть
                $stub = str_replace(
                    [
                        '',
                    ],
                    [
                    ],
                    $stub
                );

                // Записываем измененный шаблон в файл
                $this->files->put($path, $stub);
                $this->info('View created successfully.');
            }
        }
    }

    /**
     * Возвращает путь для Vue-компонента на основе переданного имени.
     *
     * Этот метод генерирует и возвращает путь к файлу Vue-компонента на основе переданного имени,
     * заменяя обратные слеши на прямые и добавляя нужные директории и расширение.
     *
     * @param string $name Имя компонента.
     * @return string Путь к файлу Vue-компонента.
     */
    protected function getVueComponentPath($name) : String
    {
        return base_path('resources/js/components/'.str_replace('\\', '/', $name).".vue");
    }

    /**
     * Возвращает пути для представлений (views) на основе переданного имени.
     *
     * Этот метод генерирует и возвращает коллекцию путей к файлам представлений (views) на основе
     * переданного имени. Для каждого типа представления (create, edit, index, show) создается
     * соответствующий путь.
     *
     * @param string $name Имя представления.
     * @return Illuminate\Support\Collection Коллекция путей к файлам представлений.
     */
    protected function getViewPath($name) : object
    {

        $arrFiles = collect([
            'create',
            'edit',
            'index',
            'show',
        ]);

        $paths = $arrFiles->map(function($item) use ($name){
            return base_path('resources/views/'.str_replace('\\', '/', $name).'/'.$item.".blade.php");
        });

        return $paths;
    }

    /**
     * Возвращает путь для файла контроллера на основе переданного аргумента имени.
     *
     * Этот метод генерирует и возвращает путь к файлу контроллера на основе переданного аргумента имени.
     * Имя файла контроллера формируется из имени класса с использованием стандартной структуры и расширения '.php'.
     *
     * @param string $argument Аргумент имени.
     * @return string Путь к файлу контроллера.
     */
    private function getControllerPath($argument)
    {
        $controller = Str::studly(class_basename($argument));
        return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $argument)."/Controllers/"."{$controller}Controller.php";
    }

    /**
     * Возвращает путь для файла API-контроллера на основе переданного имени.
     *
     * Этот метод генерирует и возвращает путь к файлу API-контроллера на основе переданного имени.
     * Имя файла API-контроллера формируется из имени класса с использованием структуры директорий и расширения '.php'.
     *
     * @param string $name Имя компонента.
     * @return string Путь к файлу API-контроллера.
     */
    private function getApiControllerPath($name)
    {
        $controller = Str::studly(class_basename($name));
        return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $name)."/Controllers/Api/"."{$controller}Controller.php";
    }

    /**
     * Создает директорию (папку) по указанному пути.
     *
     * Этот метод проверяет, существует ли директория по указанному пути. Если директория не существует,
     * он создает ее вместе с необходимыми родительскими директориями.
     *
     * @param string $path Путь к директории.
     * @return string Путь к созданной директории.
     */
    private function makeDirectory($path)
    {
        if (!$this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    /**
     * Создает маршруты для контроллера на основе переданных имен контроллера и модели.
     *
     * Этот метод генерирует маршруты для контроллера и связанной с ним модели. Он использует шаблон
     * маршрутов для замены заполнителей на реальные значения и записывает получившийся контент в файл.
     *
     * @param string $controller Имя контроллера.
     * @param string $modelName Имя модели.
     * @return void
     */
    private function createRoutes(String $controller, String $modelName) : void
    {

        $routePath = $this->getRoutesPath($this->argument('name'));

        if ($this->alreadyExists($routePath)) {
            $this->error('Routes already exists!');
        } else {

            $this->makeDirectory($routePath);

            $stub = $this->files->get(base_path('resources/stubs/routes.web.stub'));

            $stub = str_replace(
                [
                    'DummyClass',
                    'DummyRoutePrefix',
                    'DummyModelVariable',
                ],
                [
                    $controller.'Controller',
                    Str::plural(Str::snake(lcfirst($modelName), '-')),
                    lcfirst($modelName)
                ],
                $stub
            );

            $this->files->put($routePath, $stub);
            $this->info('Routes created successfully.');
        }
    }

    /**
     * Создает маршруты для API-контроллера на основе переданных имен контроллера и модели.
     *
     * Этот метод генерирует маршруты для API-контроллера и связанной с ним модели. Он использует шаблон
     * маршрутов для API для замены заполнителей на реальные значения и записывает получившийся контент в файл.
     *
     * @param string $controller Имя контроллера.
     * @param string $modelName Имя модели.
     * @return void
     */
    private function createApiRoutes(String $controller, String $modelName) : void
    {

        $routePath = $this->getApiRoutesPath($this->argument('name'));

        if ($this->alreadyExists($routePath)) {
            $this->error('Routes already exists!');
        } else {

            $this->makeDirectory($routePath);

            $stub = $this->files->get(base_path('resources/stubs/routes.api.stub'));

            $stub = str_replace(
                [
                    'DummyClass',
                    'DummyRoutePrefix',
                    'DummyModelVariable',
                ],
                [
                    'Api\\'.$controller.'Controller',
                    Str::plural(Str::snake(lcfirst($modelName), '-')),
                    lcfirst($modelName)
                ],
                $stub
            );

            $this->files->put($routePath, $stub);
            $this->info('Routes created successfully.');
        }

    }

    /**
     * Возвращает путь для файла маршрутов API на основе переданного имени.
     *
     * Этот метод генерирует и возвращает путь к файлу маршрутов API на основе переданного имени.
     * Путь формируется из структуры директорий и имени файла.
     *
     * @param string $name Имя компонента.
     * @return string Путь к файлу маршрутов API.
     */
    private function getApiRoutesPath($name) : string
    {
        return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $name)."/Routes/api.php";

    }

    /**
     * Возвращает путь для файла веб-маршрутов на основе переданного имени.
     *
     * Этот метод генерирует и возвращает путь к файлу веб-маршрутов на основе переданного имени.
     * Путь формируется из структуры директорий и имени файла.
     *
     * @param string $name Имя компонента.
     * @return string Путь к файлу веб-маршрутов.
     */
    private function getRoutesPath($name) : string
    {
        return $this->laravel['path'].'/Modules/'.str_replace('\\', '/', $name)."/Routes/web.php";

    }

    /**
     * Проверяет, существует ли файл или директория по указанному пути.
     *
     * Этот метод проверяет, существует ли файл или директория по указанному пути.
     *
     * @param string $path Путь к файлу или директории.
     * @return bool Возвращает true, если файл или директория существуют, иначе false.
     */
    protected function alreadyExists($path) : bool
    {
        return $this->files->exists($path);
    }
}
