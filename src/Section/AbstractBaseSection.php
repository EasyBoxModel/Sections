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

    /**
     * Stores the section Field objects
     * Array
     */
    protected $fields = [];

    /**
     * Sets the POST method action for form elements
     * string
     */
    protected $onPostActionString = null;

    /**
     * The section URL slug
     * String
     */
    protected $slug = null;

    /**
     * The section template name
     * Used in the getTemplate method
     *
     * String
     */
    protected $template = '';

    /**
     * The section template location in the views directory
     * Used in the getTemplate method
     *
     * String
     */
    protected $templateLocation = '';

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

    public function onEnter()
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

    /**
     * Loops through each section and returns the incomplete section
     *
     * @return AbstractBaseSection
     */
    public function getPendingSection()
    {
        $sections = $this->getSections();

        if (empty($sections)) {
            return null;
        }

        foreach ($sections as $section) {
            $section->setFields();

            if (!$section->isComplete()) {
                return $section;
            }
        }

        return null;
    }

    public function getTemplate(): String
    {
        return $this->templateLocation . '/' . $this->template;
    }
}
