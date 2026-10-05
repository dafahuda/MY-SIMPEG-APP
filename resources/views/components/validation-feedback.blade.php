@if ($errors->any())
    <div class="simpeg-validation-summary" role="alert" tabindex="-1">
        <strong>Periksa kembali data yang diisi.</strong>
        <ul>
            @foreach ($errors->all() as $message)
                <li>{{ $message }}</li>
            @endforeach
        </ul>
    </div>
    <script type="application/json" id="validation-data">{!! json_encode($errors->getMessages(), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endif
