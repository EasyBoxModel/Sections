<?php
  $name = $field->getAlias();
?>

<fieldset class="form-group">
  <label for="{{ $name }}">{{ $field->getLabel() }}</label>
  <textarea
    name="{{ $name }}"
    id="{{ $name }}"
    placeholder="{{ $field->getPlaceholder() }}"
    {{ $field->isRequired() ? 'required' : '' }}
    {{ isset($autofocus) && $autofocus ? 'autofocus' : '' }}
    class="{{ $field->getClass() }} {{ $errors->has($name) ? 'is-invalid' : '' }}">{{ old($name) ? old($name) : $field->getValue() }}</textarea>
  @if ($errors->has($name))
    <span class="invalid-feedback">
      <strong>{{ $errors->first($name) }}</strong>
    </span>
  @else
    <small class="help-block">{{ $field->getHint() }}</small>
  @endif
</fieldset>
