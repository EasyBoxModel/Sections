<?php
  $name = $field->getAlias();
?>

<fieldset class="form-group">
  <label for="{{ $name }}" class="{{ $field->getLabelClass() }}">{{ $field->getLabel() }}</label>
  <input
    id="{{ $name }}"
    name="{{ $name }}"
    type="{{ $field->getType() }}"
    placeholder="{{ $field->getPlaceholder() }}"
    {{ $field->isRequired() ? 'required' : '' }}
    {{ isset($autofocus) && $autofocus ? 'autofocus' : '' }}
    class="{{ $field->getClass() }} {{ $errors->has($name) ? 'is-invalid' : '' }}"
    value="{{ old($name) ? old($name) : $field->getValue() }}">
  @if ($errors->has($name))
    <span class="invalid-feedback">
      <strong>{{ $errors->first($name) }}</strong>
    </span>
  @else
    <small class="help-block">{{ $field->getHint() }}</small>
  @endif
</fieldset>
