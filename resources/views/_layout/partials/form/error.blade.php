@if($errors->has($field))
<div class="invalid-feedback">
    <strong>{{ $errors->first($field) }}</strong>
</div>
@endif
