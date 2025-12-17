<div class="toast-container position-fixed top-0 end-0 p-3">

    {{-- Success Toast --}}
    @if(session('success'))
        <div class="toast align-items-center text-bg-success border-0 show">
            <div class="d-flex">
                <div class="toast-body">
                    {{ session('success') }}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    {{-- Error Toast --}}
    @if($errors->any())
        <div class="toast align-items-center text-bg-danger border-0 show">
            <div class="toast-body">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

</div>
