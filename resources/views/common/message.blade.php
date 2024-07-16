@if(session()->has('success'))
<script>
    $(document).ready(function() {
        M.toast({
            html: "{{session()->get('success')}}",
            classes: "toast-success"
        });
    })
</script>
@elseif(session()->has('warning'))
<script>
    $(document).ready(function() {
        M.toast({
            html: "{{session()->get('warning')}}",
            classes: "toast-warning"
        });
    })
</script>
@elseif(session()->has('error'))
<script>
    $(document).ready(function() {
        M.toast({
            html: "{{session()->get('error')}}",
            classes: "toast-error"
        });
    })
</script>
@endif