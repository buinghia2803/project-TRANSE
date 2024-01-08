@if(count(config('app.language')) > 0)
    <div class="d-flex align-items-center justify-content-end mt-2">
        <div class="d-flex align-items-center w-150px">
            <label for="language" class="mb-0 mr-2">
                <i class="fa fa-language fz-24px"></i>
            </label>
            <select name="language" id="language" class="form-control form-control-sm">
                @foreach(config('app.language') ?? [] as $key => $value)
                    <option
                        value="{{ $key ?? '' }}"
                        data-url="{{ request()->fullUrlWithQuery(['setLanguage' => $key]) }}"
                        {{ \App::getLocale() == $key ? 'selected' : '' }}
                    >{{ __($value['label'] ?? '') }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <script type="text/javascript">
        document.querySelector('#language').addEventListener('change', function(e){
            window.location = this.options[this.selectedIndex].getAttribute('data-url');
        });
    </script>
@endif
