<?php

namespace EBM\Form;

use EBM\Field\Field;
use EBM\Exception\FieldException;
use Illuminate\Support\Facades\Log;

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

    public function getField(String $alias): Field
    {
        $fields = $this->getFields();

        return $fields[$alias];
    }

    public function getOnPostActionString(): string
    {
        return $this->onPostActionString;
    }

    public function save()
    {
        $data = request()->all();

        if (isset($data['_token'])) {
            array_shift($data);
        }

        foreach ($data as $key => $value) {
            $field = $this->getField($key);

            try {
                if (isset($value['key'])) {
                    $value = $value['key'];
                }

                $field->save($value);
            } catch (FieldException $e) {
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
