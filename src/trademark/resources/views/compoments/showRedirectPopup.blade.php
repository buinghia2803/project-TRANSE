<script>
    const routeRedirect = '{{ $routeRedirect ?? route('admin.home') }}';
    const messageFlagRoleSeki = '{{ $messages ?? '' }}';
    const labelBack = '{{ $labelBack ?? __('labels.back') }}';

    $.confirm({
        title: '',
        content: messageFlagRoleSeki,
        buttons: {
            ok: {
                text: labelBack,
                btnClass: 'btn-blue',
                action: function () {
                    window.location.href = routeRedirect
                }
            }
        }
    });
</script>
