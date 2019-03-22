<div class="invalid-feedback">
    @if($errors->has($field))
        <strong>{{ $errors->first($field) }}</strong>
    @endif
</div>
