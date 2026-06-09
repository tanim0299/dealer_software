<script>
    @if(session('success'))
        toastr.success(@json(session('success')));
    @endif

    @if(session('error'))
        toastr.error(@json(session('error')));
    @endif

    @if(session('info'))
        toastr.info(@json(session('info')));
    @endif

    @if(session('warning'))
        toastr.warning(@json(session('warning')));
    @endif

    @if($errors->any())
        @foreach($errors->all() as $message)
            toastr.error(@json($message));
        @endforeach
    @endif
</script>
