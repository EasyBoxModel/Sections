<?php
  $name = $field->getAlias();
?>

<fieldset class="form-group {{ $errors->has($name) ? 'has-danger' : '' }}">
  <label for="{{ $name }}">{{ $field->getLabel() }}</label>
  <select
    id="{{ $name }}"
    name="{{ $name }}"
    type="select"
    placeholder="{{ $field->getPlaceholder() }}"
    {{ $field->isRequired() ? 'required' : '' }}
    {{ isset($autofocus) && $autofocus ? 'autofocus' : '' }}
    class="{{ $field->getClass() }}"
    value="{{ old($name) ? old($name) : $field->getValue() }}">
    @foreach ($field->getOptions() as $option)
      <option
        value="{{ $option['key'] }}"
        {{ ($option['key'] == $field->getValue()) ? 'selected' : '' }}>{{ $option['value'] }}
      </option>
    @endforeach
  </select>
  @if ($errors->has($name))
    <span class="help-block">
      <strong>{{ $errors->first($name) }}</strong>
    </span>
  @endif
</fieldset>
