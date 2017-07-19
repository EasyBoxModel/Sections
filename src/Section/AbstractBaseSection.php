<?php

namespace EBM\Section;

use EBM\Field\Field;
use EBM\Form\FormActionTrait;
use EBM\ModelAdapters\UserAdapter as User;
use EBM\UIApplication\AbstractUIApplication;
use EBM\Exception\SectionException;

abstract class AbstractBaseSection
{
    use FormActionTrait;

    protected $fields = [];
    protected $onPostActionString = null;
    protected $slug = null;

    protected $uiApplication = null;

    abstract public function setFields();

    public function __construct(AbstractUIApplication $uiApplication)
    {
        if (!$this->hasSlug()) {
            $class = get_class($this);
            throw new SectionException("No slug defined for section [$class]");
        }

        $this->setUiApplication($uiApplication);

        $this->setOnPostActionString();
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

    public function hasSlug(): bool
    {
        return $this->slug != null;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setOnPostActionString()
    {
        $this->onPostActionString = $this->getSlug();

        return $this;
    }

    public function onComplete()
    {
        return $this;
    }

    /**
     * Loops through each section field and checks if the field has a valid value from DB
     *
     * @return Bool
     */
    public function isComplete(): bool
    {
        $fields = $this->getFields();

        if (empty($fields)) {
            return true;
        }

        foreach ($fields as $field) {
            if (!$field->isComplete()) {
                return false;
            }
        }

        return true;
    }
}
