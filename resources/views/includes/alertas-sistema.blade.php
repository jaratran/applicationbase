@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                @foreach (explode("\n", $error) as $linea_error)
                    <li class="d-block">{{ $linea_error }}</li>
                @endforeach
            @endforeach
        </ul>
    </div>
@endif

@if (session('status'))
    <div class="alert alert-success">
        @if (is_array(session('status')))
            <ul class="mb-0">
                @foreach (session('status') as $msg)
                    <li>
                        @foreach (explode("\n", $msg) as $linea_status)
                            <div class="d-block">{{ $linea_status }}</div>
                        @endforeach
                    </li>
                @endforeach
            </ul>
        @else
            @foreach (explode("\n", session('status')) as $linea)
                <div class="d-block">{{ $linea }}</div>
            @endforeach
        @endif
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger">
        @if (is_array(session('error')))
            <ul class="mb-0">
                @foreach (session('error') as $msg)
                    <li>
                        @foreach (explode("\n", $msg) as $linea_error)
                            <div class="d-block">{{ $linea_error }}</div>
                        @endforeach
                    </li>
                @endforeach
            </ul>
        @else
            @foreach (explode("\n", session('error')) as $linea)
                <div class="d-block">{{ $linea }}</div>
            @endforeach
        @endif
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning">
        @if (is_array(session('warning')))
            <ul class="mb-0">
                @foreach (session('warning') as $msg)
                    <li>
                        @foreach (explode("\n", $msg) as $linea_warning)
                            <div class="d-block">{{ $linea_warning }}</div>
                        @endforeach
                    </li>
                @endforeach
            </ul>
        @else
            @foreach (explode("\n", session('warning')) as $linea)
                <div class="d-block">{{ $linea }}</div>
            @endforeach
        @endif
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info">
        @if (is_array(session('info')))
            <ul class="mb-0">
                @foreach (session('info') as $msg)
                    <li>
                        @foreach (explode("\n", $msg) as $linea_info)
                            <div class="d-block">{{ $linea_info }}</div>
                        @endforeach
                    </li>
                @endforeach
            </ul>
        @else
            @foreach (explode("\n", session('info')) as $linea)
                <div class="d-block">{{ $linea }}</div>
            @endforeach
        @endif
    </div>
@endif
