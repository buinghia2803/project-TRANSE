@extends('admin.layouts.app')

@section('main-content')

   <div class="content-wrapper">

        @include('admin.components.includes.content-header', [
            'page_title' => __('labels.user_list'),
            'breadcrumbs' => [
                [ 'label' => __('labels.user_list'), 'active' => true ],
            ],
        ])

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        @include('admin.components.includes.messages')
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        @include('admin.components.includes.search', [
                            'fields' => [
                                [
                                    'type' => SEARCH_TEXT,
                                    'name' => 'id',
                                    'label' => __('labels.id')
                                ],
                                [
                                    'type' => SEARCH_TEXT,
                                    'name' => 'name',
                                    'label' => __('labels.user_name')
                                ],
                                [
                                    'type' => SEARCH_TEXT,
                                    'name' => 'email',
                                    'label' => __('labels.email')
                                ],
                                [
                                    'type' => SEARCH_SELECT,
                                    'name' => 'status',
                                    'label' => __('labels.status'),
                                    'options' => $status
                                ],
                                [
                                    'type' => SEARCH_DATERANGE,
                                    'name' => 'created_at',
                                    'label' => __('labels.created_at')
                                ],
                            ]
                        ])
                    </div>
                </div>

                <div class="row mb-3 px-1">
                    <div class="col-12 px-4">
                        @if(in_array('user.store', $authPermissions))
                            <a href="{{ route('admin.user.create') }}" class="btn btn-primary btn-sm float-right">
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
                                            <th class="mw-200px">{{ __('labels.user_name') }}</th>
                                            <th class="mw-200px">{{ __('labels.email') }}</th>
                                            <th class="mw-200px">{{ __('labels.role') }}</th>
                                            <th class="w-150px mw-150px">{{ __('labels.status') }}</th>
                                            <th class="w-200px mw-200px">{{ __('labels.created_at') }}</th>
                                            @if(in_array('user.update', $authPermissions) || in_array('user.destroy', $authPermissions))
                                                <th class="w-120px mw-120px text-center">{{ __('labels.action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users as $item)
                                            <tr>
                                                <td class="text-center">{{ $item->id ?? '' }}</td>
                                                <td>
                                                    <p class="mb-0 line line-2">{{ $item->getName() ?? '' }}</p>
                                                </td>
                                                <td>{{ $item->email ?? '' }}</td>
                                                <td>
                                                    @foreach ($item->roles as $role)
                                                        <h5 class="mb-0 d-inline"><span class="badge badge-info">{{ $role->name }}</span></h5>
                                                    @endforeach
                                                </td>
                                                <td>{{ $status[$item->status] ?? '' }}</td>
                                                <td>{{ $item->created_at->format('Y/m/d H:i:s') ?? '' }}</td>
                                                @if(in_array('user.update', $authPermissions) || in_array('user.destroy', $authPermissions))
                                                    <td>
                                                        @if(in_array('user.update', $authPermissions))
                                                            <a href="{{ route('admin.user.edit', $item->id) }}" class="btn btn-info btn-sm w-100 mb-1">
                                                                {{ __('labels.edit') }}
                                                            </a>
                                                        @endif

                                                        @if(in_array('user.destroy', $authPermissions))
                                                            <form action="{{ route('admin.user.destroy', $item->id) }}" id="delete-{{ $item->id }}" method="post">
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
                                                <td colspan="100%" class="text-center font-weight-bold">
                                                    {{ __((request()->search == SEARCH) ? 'labels.no_search_result' : 'labels.no_record') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="card-footer clearfix">
                                <div class="pagination-footer pagination-sm float-right">
                                    {{ $users->appends(Request()->all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
