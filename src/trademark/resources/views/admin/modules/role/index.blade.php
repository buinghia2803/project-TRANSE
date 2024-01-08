@extends('admin.layouts.app')

@section('main-content')
    <div class="content-wrapper">

        @include('admin.components.includes.content-header', [
            'page_title' => 'labels.role_management',
            'breadcrumbs' => [
                [ 'label' => 'labels.role_management', 'active' => true ],
            ],
        ])

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        @include('admin.components.includes.messages')
                    </div>
                </div>

                <div class="row mb-3 px-1">
                    <div class="col-12 px-4">
                        @if(in_array('role.store', $authPermissions))
                            <a href="{{ route('admin.role.create') }}" class="btn btn-primary btn-sm float-right">
                                {{ __('labels.create') }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body table-responsive p-0">
                                <table class="table table-striped table-bordered table-custom">
                                    <thead>
                                        <tr>
                                            <th class="w-50px mw-50px text-center">{{ __('labels.id') }}</th>
                                            <th class="mw-200px">{{ __('labels.role_name') }}</th>
                                            @if(in_array('role.update', $authPermissions) || in_array('role.destroy', $authPermissions))
                                                <th class="w-120px mw-120px text-center">{{ __('labels.action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($roles as $item)
                                            <tr>
                                                <td class="text-center">{{ $item->id ?? '' }}</td>
                                                <td>{{ $item->name ?? '' }}</td>
                                                @if(in_array('role.update', $authPermissions) || in_array('role.destroy', $authPermissions))
                                                    <td>
                                                        @if(in_array('role.update', $authPermissions))
                                                            <a href="{{ route('admin.role.edit', $item->id) }}" class="btn btn-info btn-sm w-100 mb-1">
                                                                {{ __('labels.edit') }}
                                                            </a>
                                                        @endif

                                                        @if(in_array('role.destroy', $authPermissions))
                                                            <form action="{{ route('admin.role.destroy', $item->id) }}" id="delete-{{ $item->id }}" method="post">
                                                                @csrf
                                                                {{ method_field('DELETE') }}

                                                                <button
                                                                    type="button"
                                                                    class="btn btn-danger btn-sm w-100"
                                                                    onclick="submitForm('#delete-{{ $item->id }}', '{{ __('labels.delete_question') }}')"
                                                                >{{ __('labels.delete') }}</button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="100%" class="text-center font-weight-bold">{{ __('labels.no_record') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix">
                                <div class="pagination-footer pagination-sm float-right">
                                    {{ $roles->appends(Request()->all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
