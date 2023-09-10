<?php

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Modules\Admin\Dashboard\Classes\Base;
use Illuminate\Http\Request;

class DashboardController extends Base
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //dd(__('admin.dashboard_title'));
        $this->title = __("admin.dashboard_title_page");
        $this->content = view('Admin::Dashboard.index')->with([
            'title' => $this->title,
        ])->render();

        return $this->renderOutput();
    }
}
