<?php

namespace EBM\Section;

use EBM\Field\Field;
use EBM\Form\FormActionTrait;
use EBM\ModelAdapters\UserAdapter as User;
use EBM\UIApplication\AbstractUIApplication;

abstract class AbstractBaseSection
{
    use FormActionTrait;

    protected $fields = [];
    protected $onPostActionString = '';
    protected $slug = '';

    protected $uiApplication = null;

    abstract public function setSlug();
    abstract public function setFields();
    abstract public function setOnPostActionString();

    public function __construct(AbstractUIApplication $uiApplication)
    {
        $this->setSlug();
        $this->setOnPostActionString();
        $this->setUiApplication($uiApplication);
    }

    public function setUIApplication(AbstractUIApplication $uiApplication)
    {
        $this->uiApplication = $uiApplication;
        
        return $this;
    }

    public function getUIApplication()
    {
        return $this->uiApplication;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
