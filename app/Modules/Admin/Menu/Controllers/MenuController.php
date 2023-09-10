<?php

namespace App\Modules\Admin\Menu\Controllers;

use App\Modules\Admin\Menu\Models\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $menus = Menu::paginate(5);
        return view('Admin::Menu.index', compact('menus'));
    }

    /**
     * Create of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $menuIds = Menu::pluck('id');
        return view('Admin::Menu.create', compact('menuIds'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'path' => 'required',
            'parent' => 'required|integer',
            'type' => 'required',
            'sort_order' => 'integer',
        ]);

        Menu::create($request->all());
        return redirect()->route('menus.store')->with('success', __('menu.notification_menu_added'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Modules\Admin\Menu\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function edit(Menu $menu)
    {
        $menu->find('id');
        return view('Admin::Menu.edit', compact('menu'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Modules\Admin\Menu\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'title' => 'required',
            'path' => 'required',
            'parent' => 'required|integer',
            'type' => 'required',
            'sort_order' => 'integer',
        ]);

        $menu->update($request->all());
        return redirect()->route('menus.index')->with('success', __('menu.notification_menu_change'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Modules\Admin\Menu\Models\Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        if ($menu->children->count() > 0) {
            return redirect()->route('menus.index')->with('error', __('menu.error_menu'));
        }

        $menu->delete();
        return redirect()->route('menus.index')->with('success', __('menu.removed_menu'));
    }
}
