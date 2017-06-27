<?php

namespace EBM\Model;

class LuGender
{
    const MALE = 1;
    const FEMALE = 2;

    const OPTIONS = [
        [
            'key' => self::MALE,
            'value' => 'Masculino',
        ],
        [
            'key' => self::FEMALE,
            'value' => 'Femenino',
        ]
    ];
}
