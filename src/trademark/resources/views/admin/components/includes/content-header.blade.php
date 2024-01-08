<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                @include('admin.components.includes.breadcrumb', [
                    'breadcrumbs' => $breadcrumbs ?? [],
                ])
                <h1 class="m-0 mt-2">{{ __($page_title ?? '') }}</h1>
            </div>
        </div>
    </div>
</div>
