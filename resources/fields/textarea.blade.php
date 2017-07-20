<?php
  $name = $field->getAlias();
?>

<fieldset class="form-group {{ $errors->has($name) ? ' has-danger' : '' }}">
  <label for="{{ $name }}">{{ $field->getLabel() }}</label>
  <textarea
    name="{{ $name }}"
    id="{{ $name }}"
    placeholder="{{ $field->getPlaceholder() }}"
    {{ $field->isRequired() ? 'required' : '' }}
    {{ isset($autofocus) && $autofocus ? 'autofocus' : '' }}
    class="{{ $field->getClass() }}">{{ old($name) ? old($name) : $field->getValue() }}</textarea>
  @if ($errors->has($name))
    <span class="help-block">
      <strong>{{ $errors->first($name) }}</strong>
    </span>
  @else
    <small class="help-block">{{ $field->getHint() }}</small>
  @endif
</fieldset>
