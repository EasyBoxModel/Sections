<?php

namespace EBM\Form;

use EBM\Field\Field;
use EBM\Exception\FieldException;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

trait FormActionTrait
{
    protected $hasError = false;

    protected $error = [];

    public function addField(String $alias): Field
    {
        $field = new Field;

        $field->setAlias($alias);

        $field->setName($alias);

        $this->fields[$alias] = $field;

        return $field;
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function getField(String $alias)
    {
        $fields = $this->getFields();

        return isset($fields[$alias]) ? $fields[$alias] : null;
    }

    public function getOnPostActionString(): string
    {
        return $this->onPostActionString;
    }

    public function save()
    {
        $data = request()->all();

        foreach ($data as $key => $value) {
            $field = $this->getField($key);

            if (!$field) continue;

            if ($field->isUnset()) continue;

            if (is_array($value)) {
                if ($field->getType() == Field::TYPE_CHECKBOX) {
                    $this->saveCheckboxOptions($key, $value); continue;
                }

                $this->saveRadioOption($key, $value); continue;
            }

            $field->setValue($value);

            try {

                if ($field->hasSaveStrategy()) {
                    $field->saveWithStrategy(); continue;
                }

                $field->save();

            } catch (QueryException $e) {
                Log::critical($e->getMessage());
                return $this->setError([
                    'message' => 'No hemos podido guardar tus datos. Intenta de nuevo.',
                    ]);
            } catch (FieldException $e) {
                Log::critical($e->getMessage());
                return $this->setError([
                    'message' => $e->getMessage(),
                    ]);
            } catch (\Exception $e) {
                Log::critical($e->getMessage());
                return $this->setError([
                    'message' => 'No hemos podido guardar tus datos. Intenta de nuevo.',
                    ]);
            }
        }

        return $this;
    }

    public function saveRadioOption(String $fieldName = '', Array $data = [])
    {
        foreach ($data as $key => $value) {
            $field = $this->getField($fieldName);

            if (!$field) continue;

            try {

                $field->setValue($value)->save();

            } catch (QueryException $e) {
                Log::critical($e->getMessage());
                return $this->setError([
                    'message' => 'No hemos podido guardar tus datos. Intenta de nuevo.',
                    ]);
            } catch (FieldException $e) {
                Log::critical($e->getMessage());
                return $this->setError([
                    'message' => $e->getMessage(),
                    ]);
            } catch (\Exception $e) {
                Log::critical($e->getMessage());
                return $this->setError([
                    'message' => 'No hemos podido guardar tus datos. Intenta de nuevo.',
                    ]);
            }
        }

        return $this;
    }

    public function saveCheckboxOptions(String $fieldName = '', Array $data = [])
    {
        $field = $this->getField($fieldName);

        if (!$field) {
            return $this;
        }

        try {

            $field->setDividedStringValue($data)->save();

        } catch (QueryException $e) {
            Log::critical($e->getMessage());
            return $this->setError([
                'message' => 'No hemos podido guardar tus datos. Intenta de nuevo.',
                ]);
        } catch (FieldException $e) {
            Log::critical($e->getMessage());
            return $this->setError([
                'message' => $e->getMessage(),
                ]);
        } catch (\Exception $e) {
            Log::critical($e->getMessage());
            return $this->setError([
                'message' => 'No hemos podido guardar tus datos. Intenta de nuevo.',
                ]);
        }

        return $this;
    }

    public function setError(Array $error)
    {
        $this->hasError = true;

        $this->error = $error;

        return $this;
    }

    public function hasError(): bool
    {
        return $this->hasError;
    }

    public function getErrorMessage(): string
    {
        return $this->error['message'];
    }

    public function getValidationRules()
    {
        return [];
    }

    public function getValidationMessages()
    {
        return [];
    }
}
