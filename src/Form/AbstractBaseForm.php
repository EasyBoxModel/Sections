<?php

namespace EBM\Form;

use EBM\Field\Field;

abstract class AbstractBaseForm
{
    use FormActionTrait;

    protected $fields = [];
    protected $onPostActionString = '';

    abstract public function setFields();
    abstract public function setOnPostActionString();

    public function __construct()
    {
        $this->setOnPostActionString();
    }
}
