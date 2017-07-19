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

            try {

                if (isset($value['key'])) {
                    $value = $value['key'];
                }

                $field->save($value);

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
