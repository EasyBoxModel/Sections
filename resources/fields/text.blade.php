<?php
  $name = $field->getAlias();
?>

<fieldset class="form-group {{ $errors->has($name) ? ' has-danger' : '' }}">
  <label for="{{ $name }}">{{ $field->getLabel() }}</label>
  <input
    id="{{ $name }}"
    name="{{ $name }}"
    type="{{ $field->getType() }}"
    placeholder="{{ $field->getPlaceholder() }}"
    {{ $field->isRequired() ? 'required' : '' }}
    {{ isset($autofocus) && $autofocus ? 'autofocus' : '' }}
    class="{{ $field->getClass() }}"
    value="{{ old($name) ? old($name) : $field->getValue() }}">
  @if ($errors->has($name))
    <span class="help-block">
      <strong>{{ $errors->first($name) }}</strong>
    </span>
  @endif
</fieldset>
