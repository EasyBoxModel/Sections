<?php

namespace EBM\Section;

use EBM\Field\Field;
use EBM\Utils\RegexUtil;
use EBM\Model\LuGender;

class ExampleSection extends AbstractBaseSection
{
    protected $slug = 'example-section';

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
