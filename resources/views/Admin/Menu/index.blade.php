@extends('Admin::layouts.menu.layout')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('menu.title') }}</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="content">

        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('menu.card_title') }}</h3>
                        </div>
                        <div class="card-body">
                            <a href="{{ route('menus.create') }}" class="btn btn-primary mb-3">
                                {{ __('menu.add_menu_button') }}
                            </a>
                            @if (count($menus))
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover text-nowrap">
                                        <thead>
                                        <tr>
                                            <th style="width: 30px">#</th>
                                            <th>{{ __('menu.name_menu') }}</th>
                                            <th>{{ __('menu.path_menu') }}</th>
                                            <th>{{ __('menu.parent_menu') }}</th>
                                            <th>{{ __('menu.type_menu') }}</th>
                                            <th>{{ __('menu.sort_order') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($menus as $menu)
                                            <tr>
                                                <td>{{ $menu->id }}</td>
                                                <td>{{ $menu->title }}</td>
                                                <td>{{ $menu->path }}</td>
                                                <td>{{ $menu->parent }}</td>
                                                <td>{{ $menu->type }}</td>
                                                <td>{{ $menu->sort_order }}</td>
                                                <td>
                                                    <a href="{{ route('menus.edit', ['menu' => $menu->id]) }}" class="btn btn-info btn-sm float-left mr-1">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </a>
                                                    <form action="{{ route('menus.delete', ['menu' => $menu->id]) }}" method="post" class="float-left">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Подтвердите удаление')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p>{{ __('menu.no_data') }}</p>
                            @endif
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer clearfix">
                            {{ $menus->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->
@endsection
