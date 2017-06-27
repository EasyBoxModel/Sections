# EBM Sections

A PHP composer package for bootstraping web applications onboarding flows

## Example

A simple questionaire would work under the route: 

`welcome/section/first-questions`

While in your controller, a UIApplication factory determines which sections to show: 

```php
<?php 

use EBM\UIApplication\Factory;

class Controller
{
    public function welcome()
    {
        $uiApplication = Factory::get();

        $uiApplication->registerInstance('user', new UserModel);

        $slug = $this->catchRouteParams(); // first-questions

        $section = $uiApplication
                    ->getSectionBySlug($slug)
                    ->setFields();

        return view('welcome/section', compact('section'));
    }
}
```

The factory will be in charge of delivering the right UIApplication where the corresponding sections are defined:

```php
<?php 

namespace EBM\UIApplication;

class UIApplication_1 extends AbstractUIApplication
{
    public $sections = [
        'EBM\Section\FirstQuestions',
    ];
}
```

And each of the section will contain the configuration that you need when the UI is presented to the user:

```php

namespace EBM\Section;

use EBM\Field\Field;
use Utils\RegexUtil;
use Model\LuGender;

class FirstQuestions extends AbstractBaseSection
{
    public function setSlug()
    {
        $this->slug = 'first-questions';

        return $this;
    }

    public function setOnPostActionString()
    {
        $this->onPostActionString = 'post-section';

        return $this;
    }

    public function setFields()
    {
        $user = $this->getUIApplication()->getInstance('user');

        $this->addField('name')
            ->setModel($user)
            ->setLabel('Nombre')
            ->setType(Field::TYPE_TEXT)
            ->required()
            ->setValue(null);

        $this->addField('paternal_last_name')
            ->setModel($user)
            ->setLabel('Apellido paterno')
            ->setType(Field::TYPE_TEXT)
            ->required()
            ->setValue(null);

        $this->addField('maternal_last_name')
            ->setModel($user)
            ->setLabel('Apellido materno')
            ->setType(Field::TYPE_TEXT)
            ->required()
            ->setValue(null);

        $this->addField('mobile_number')
            ->setModel($user)
            ->setLabel('Teléfono celular')
            ->setType(Field::TYPE_TEXT)
            ->required()
            ->setValue(null)
            ->setPlaceholder('eg. +502 1234 567 890');

        $this->addField('dob')
            ->setModel($user)
            ->setLabel('Fecha de nacimiento')
            ->setType(Field::TYPE_DATE)
            ->required()
            ->setValue(null)
            ->setPlaceholder('DD/MM/AAAA');

        $this->addField('gender_id')
            ->setModel($user)
            ->setOptions(LuGender::OPTIONS)
            ->setLabel('Género')
            ->setType(Field::TYPE_RADIO)
            ->required()
            ->setValue(null);

        return $this;
    }

    public function getValidationRules()
    {
        $phonePattern = RegexUtil::PHONE_PATTERN;

        return [
            'dob' => 'required|date',
            'gender_id' => 'required|regex:/^[1,2]/i',
            'name' => 'required|string|max:40',
            'paternal_last_name' => 'required|string|max:40',
            'maternal_last_name' => 'required|string|max:40',
            'mobile_number' => "required|string|regex:/$phonePattern/", // +5211234567890
        ];
    }

    public function getValidationMessages()
    {
        return [
            'dob.date' => 'El campo debe ser una fecha válida',
            'mobile_number.regex' => 'El teléfono debe contener código de país y 10 dígitos para el número',
        ];
    }
}
```

Where it gets really interesting is on the views, where you can render each of your sections by simply creating a wrapper: 

```html
@extends('layouts.app')

@section('head-link')
  <link href="{{ asset('css/front/application/section.css') }}" rel="stylesheet">
@endsection

@section('content')
  @include('front/sections/' . $section->getSlug(), ['section' => $section])
@endsection
```

and the section itself:

```html
<form action="{{ $section->getOnPostActionString() }}" method="POST">
  <section class="section" id="{{ $section->getSlug() }}">
    <header class="section-header">
      <div class="container text-center">
        <h1>Title</h1>
      </div>
    </header>
    <div class="section-content">
      <div class="container-md">
        <div class="grid-list grid-list-3 grid-list-1-xs">
          <article class="grid-list-item">
            @include('fields/text', ['field' => $section->getField('name')])
          </article>
          <article class="grid-list-item">
            @include('fields/text', ['field' => $section->getField('paternal_last_name')])
          </article>
          <article class="grid-list-item">
            @include('fields/text', ['field' => $section->getField('maternal_last_name')])
          </article>
        </div>
        
        @include('fields/text', ['field' => $section->getField('dob')])

        @include('fields/radio', ['field' => $section->getField('gender_id')])

        @include('fields/text', ['field' => $section->getField('mobile_number')])

      </div>
    </div>
    <footer class="section-footer">
      <div class="container-sm">
        @if ($section->hasError())
            <h5>{{ $section->getErrorMessage() }}</h5>
        @endif

        <button type="submit" class="btn btn-inverse btn-lg btn-block">Continue</button>
      </div>
    </footer>
  </section>
</form>
```

Each time you make a request, the UIApplication will deliver the right section based on your rules using the section `isComplete()` method:

```php
<?php 

use EBM\UIApplication\Factory;

class Controller
{
    public function postSection()
    {
        $uiApplication = Factory::get();

        $uiApplication->registerInstance('user', new UserModel);

        $slug = $this->catchRouteParams(); // first-questions

        $section = $uiApplication
                    ->getSectionBySlug($slug)
                    ->setFields();

        if (!$section->isValid()) {
            // Do something
        }

        $section->save(); // have control over how each section is saved or simply go through each field

        return redirect('welcome', ['slug' => $uiApplication->getNextSectionSlug()]);
    }
}
```

## EBM Fields

Additionally, boilerplate section fields are provided based on your section `setFields()` configuration: 

```php
public function setFields()
{
    $user = $this->getUIApplication()->getInstance('user');

    $this->addField('name')
        ->setModel($user)
        ->setLabel('Nombre')
        ->setType(Field::TYPE_TEXT)
        ->required()
        ->setValue(null);

    $this->addField('paternal_last_name')
        ->setModel($user)
        ->setLabel('Apellido paterno')
        ->setType(Field::TYPE_TEXT)
        ->required()
        ->setValue(null);

    $this->addField('maternal_last_name')
        ->setModel($user)
        ->setLabel('Apellido materno')
        ->setType(Field::TYPE_TEXT)
        ->required()
        ->setValue(null);

    $this->addField('mobile_number')
        ->setModel($user)
        ->setLabel('Teléfono celular')
        ->setType(Field::TYPE_TEXT)
        ->required()
        ->setValue(null)
        ->setPlaceholder('eg. +502 1234 567 890');

    $this->addField('dob')
        ->setModel($user)
        ->setLabel('Fecha de nacimiento')
        ->setType(Field::TYPE_DATE)
        ->required()
        ->setValue(null)
        ->setPlaceholder('DD/MM/AAAA');

    $this->addField('gender_id')
        ->setModel($user)
        ->setOptions(LuGender::OPTIONS)
        ->setLabel('Género')
        ->setType(Field::TYPE_RADIO)
        ->required()
        ->setValue(null);

    return $this;
}
```

The section fields templates must be passed a `$field` variable: 

```html
@include('fields/text', ['field' => $section->getField('name')])
```

That will work with the field of input type `text` in this case: 

```html
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
```







