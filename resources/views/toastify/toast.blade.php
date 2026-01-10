<!-- Toast Container -->
<ul class="my_custom_toast_alert"></ul>

<!-- Toast Scripts -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    @if(session('success'))
        createToast('success', "{{ session('success') }}");
    @endif

    @if(session('info'))
        createToast('info', "{{ session('info') }}");
    @endif

    @if(session('warning'))
        createToast('warning', "{{ session('warning') }}");
    @endif

    @if(session('error'))
        createToast('error', "{{ session('error') }}");
    @endif

    @if($errors->any())
        @foreach($errors->all() as $error)
            createToast('error', "{{ $error }}");
        @endforeach
    @endif
});
</script>