<script>
    document.getElementById('notificationBtn')?.addEventListener('click', function(e) {
        e.stopPropagation();
        document.getElementById('notificationDropdown').classList.toggle('hidden');
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#notificationBtn') && !e.target.closest('#notificationDropdown')) {
            document.getElementById('notificationDropdown')?.classList.add('hidden');
        }
    });
</script>