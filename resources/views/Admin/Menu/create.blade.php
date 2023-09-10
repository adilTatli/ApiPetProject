@extends('Admin::layouts.menu.layout')

@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{ __('menu.create_menu_title') }}</h1>
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
                            <h3 class="card-title">{{ __('menu.create_menu_title') }}</h3>
                        </div>

                        <form action="{{ route('menus.store') }}"
                              role="form" method="post">
                            @csrf
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title">{{ __('menu.name_menu') }}</label>
                                    <input type="text" name="title" class="form-control
                        @error('title') is-invalid @enderror" id="title" placeholder="{{ __('menu.name_menu') }}">
                                </div>

                                <div class="form-group">
                                    <label for="path">{{ __('menu.path_menu') }}</label>
                                    <input type="text" name="path" class="form-control
                        @error('title') is-invalid @enderror" id="path" placeholder="{{ __('menu.path_menu') }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ __('menu.parent_menu') }}</label>
                                    <select class="form-control select2" style="width: 100%;" name="parent">
                                        <option value="0">0</option>
                                        @foreach($menuIds as $menu)
                                            <option value="{{ $menu }}">{{ $menu }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="type">{{ __('menu.type_menu') }}</label>
                                    <input type="text" name="type" class="form-control
                        @error('title') is-invalid @enderror" id="type" placeholder="{{ __('menu.type_menu') }}">
                                </div>

                                <div class="form-group">
                                    <label for="sort_order">{{ __('menu.sort_order') }}</label>
                                    <input type="text" name="sort_order" class="form-control
                        @error('title') is-invalid @enderror" id="sort_order" placeholder="{{ __('menu.sort_order') }}">
                                </div>
                            </div>

                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary">{{ __('menu.create_button') }}</button>
                            </div>
                        </form>

                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
