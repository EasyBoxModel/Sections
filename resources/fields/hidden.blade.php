<?php
  $name = $field->getAlias();
?>

<input
  id="{{ $name }}"
  name="{{ $name }}"
  type="hidden"
  {{ $field->isRequired() ? 'required' : '' }}
  value="{{ old($name) ? old($name) : $field->getValue() }}">
