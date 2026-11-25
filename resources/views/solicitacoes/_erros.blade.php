@if($errors->any())
    <div class="alert">
        @foreach($errors->all() as $error)
            {{ $error }}<br>
        @endforeach
    </div>
@endif
