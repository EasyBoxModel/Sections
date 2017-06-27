<?php

namespace EBM\UIApplication;

class Factory
{
    const UI_APPLICATION_PLACEHOLDER = 'EBM\UIApplication\UIApplication_';

    public static function get(): AbstractUIApplication
    {
        $UIApplication = self::UI_APPLICATION_PLACEHOLDER . '1';

        return new $UIApplication;
    }
}
