<?php

namespace EBM\Field;

use EBM\Exception\FieldException;
use EBM\Field\Contract\SaveStrategyContract;

class Field
{
    private $name = null;
    private $label = null;
    private $type = null;
    private $value = null;
    private $options = [];
    private $alias = null;
    private $hint = null;
    private $placeholder = '';
    private $required = false;
    private $class = 'form-control form-control-lg';

    private $separator = '|';

    private $model = null;

    /**
     * When calling the unset() method, this field will not have a save attempt
     */
    private $isUnset = false;

    /**
     * A class static method that implements a SaveStrategyContract
     */
    private $saveStrategy = null;

    const TYPE_TEXT = 'text';
    const TYPE_SELECT = 'select';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_DATE = 'date';
    const TYPE_EMAIL = 'email';
    const TYPE_RADIO = 'radio';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_NUMBER = 'number';
    const TYPE_HIDDEN = 'hidden';

    public function setSeparator(String $separator)
    {
        $this->separator = $separator;

        return $this;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function setAlias(String $alias)
    {
        $this->alias = $alias;

        return $this;
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function setName(String $name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setLabel(String $label)
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setType(String $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setOptions(Array $options)
    {
        $this->options = $options;

        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setHint(String $hint)
    {
        $this->hint = $hint;

        return $this;
    }

    public function getHint()
    {
        return $this->hint;
    }

    public function setPlaceholder(String $ph)
    {
        $this->placeholder = $ph;

        return $this;
    }

    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    public function setClass(String $class)
    {
        $this->class = $class;

        return $this;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function required()
    {
        $this->required = true;

        return $this;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setValueFromDb()
    {
        $value = null;

        $model = $this->getModel();

        $column = $this->getName();

        if ($model != null && $column != null) {
            $model = $model;

            $value = $model->$column;
        }

        return $this->setValue($value);
    }

    public function save()
    {
        $model = $this->getModel();

        if (!$model) {
            error_log('No field model specified');
            throw new FieldException('No hemos podido guardar tus datos');
        }

        $column = $this->getName();

        if (!$column) {
            error_log('No field column specified');
            throw new FieldException('No hemos podido guardar tus datos');
        }

        $model->$column = $this->getValue();

        $model->save();

        return $this;
    }

    /**
     * [saveAsDividedString Concatenates each value to the existing model column value with the set separator]
     * @param  String|string $value [A string from a values array defined in the FormActionTrait]
     * @return [Field]
     */
    public function setDividedStringValue(Array $values = [])
    {
        $this->setValue(implode($this->getSeparator(), $values));

        return $this;
    }

    public function getValueFromDb()
    {
        $model = $this->getModel();

        $column = $this->getName();

        if (!$model || !$column) {
            return null;
        }

        return $model->$column;
    }

    public function isComplete(): bool
    {
        if ($this->isRequired()) {
            return !$this->isEmpty();
        }

        return true;
    }

    public function isEmpty(): bool
    {
        $value = $this->getValueFromDb();

        if ($value === null ||
            $value === -1 ||
            $value === '' ||
            $value === '-1' ||
            count($value) < 1) {
            return true;
        }

        return false;
    }

    /**
     * [getDividedStringValue Parses the model column divided string and returns the value if it matches the provided option]
     * @param  String $option [A string provided by the FormActionTrait saveCheckboxOptions method]
     * @return [Field]
     */
    public function getDividedStringValue(String $option): string
    {
        $model = $this->getModel();

        $column = $this->getName();

        if ($this->isEmpty()) {
            return '';
        }

        $dividedString = $model->$column;

        $values = explode($this->getSeparator(), $dividedString);

        foreach ($values as $key => $value) {
            if ($value == $option) {
                return $value;
            }
        }

        return '';
    }

    /**
     * Avoid saving this field
     */
    public function unset()
    {
        $this->isUnset = true;

        return $this;
    }

    /**
     * Determines if the Field should be saved
     */
    public function isUnset(): bool
    {
        return $this->isUnset;
    }

    /**
     * The Field has a save strategy, then apply the strategy
     */
    public function hasSaveStrategy(): bool
    {
        return $this->saveStrategy != null;
    }

    /**
     * Sets a SaveStrategyContract implementation
     *
     * @param Array [SaveStrategy::class, method]
     */
    public function setSaveStrategy(Array $strategy)
    {
        $this->saveStrategy = $strategy;

        return $this;
    }

    /**
     * Gets a SaveStrategyContract to apply to the field
     *
     * @return Array [SaveStrategy, method]
     */
    public function getSaveStrategy(): Array
    {
        return $this->saveStrategy;
    }

    /**
     * Applies a SaveStrategyContract method
     *
     * @return Field
     */
    public function saveWithStrategy()
    {
        $saveStrategy = $this->saveStrategy;

        return $saveStrategy($this);
    }
}
