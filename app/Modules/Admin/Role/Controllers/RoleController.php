<?php

namespace App\Modules\Admin\Role\Controllers;

use App\Modules\Admin\Dashboard\Classes\Base;
use App\Modules\Admin\Role\Models\Role;
use App\Modules\Admin\Role\Requests\RoleRequest;
use App\Modules\Admin\Role\Services\RoleService;
use Illuminate\Http\Response;

class RoleController extends Base
{
    public function __construct(RoleService $roleService)
    {
        parent::__construct();
        $this->service = $roleService;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', Role::class);

        $roles = Role::all();

        $this->title = __('role.title_role_index');

        $this->content = view('Admin::Role.index')->
        with([
            'roles' => $roles,
            'title' => $this->title,
        ])->
        render();

        return $this->renderOutput();
    }

    /**
     * Create of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Role::class);

        $this->title = __('role.title_role_create');

        $this->content = view('Admin::Role.create')->
        with([
            'title' => $this->title,
        ])->
        render();

        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleRequest $request
     * @return Response
     */
    public function store(RoleRequest $request)
    {
        $this->service->save($request, new Role());

        return \Redirect::route('roles.index')->with([
            'message' => __('Success'),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Modules\Admin\Role\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Modules\Admin\Role\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        $this->authorize('edit', Role::class);

        $this->title = __('role.title_role_edit');

        $this->content = view('Admin::Role.edit')->
        with([
            'title' => $this->title,
            'item' => $role,
        ])->
        render();

        return $this->renderOutput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param RoleRequest $request
     * @param Role $role
     * @return Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $this->service->save($request, $role);

        return \Redirect::route('roles.index')->with([
            'message' => __('Success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Modules\Admin\Role\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return \Redirect::route('roles.index')->with([
            'message' => __('Success'),
        ]);
    }
}
