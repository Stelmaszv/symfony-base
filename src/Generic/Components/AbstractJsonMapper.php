<?php


namespace App\Generic\Components;
abstract class AbstractJsonMapper
{
	protected bool $multi = false;

    public function isValid(array $value): bool
    {
        $maper = false;
        
        if ($this->multi && $this->is2dArray($value)) {
            $this->validMultiMapper($value);
            $maper = true;
        }

        if (!$this->multi && !$this->is2dArray($value)) {
            $this->validMapper($value);
            $maper = true;
        }

        if($maper === false){
            throw new \Exception("Invalid Json !");
        }

        return true;
    }

    private function is2dArray(array $array): bool
    {
        if (is_array($array) && count($array) > 0) {
            return is_array(array_shift($array));
        }
        return false;
    }

    private function validMultiMapper(array $value): void
    {
        foreach ($value as $el) {
            $this->validMapper($el);
        }
    }

    private function validMapper(array $value): void
    {
        foreach ($value as $jEl => $key) {
            if (!isset($this->jsonSchema()[$jEl])) {
                throw new \Exception("Invalid Json field  ".$jEl." not Exist !");
            }

            if (!$this->validType($this->jsonSchema()[$jEl], $key)) {
                throw new \Exception("Invalid Json type for ".$jEl." valid is ".$this->jsonSchema()[$jEl]." given ".$key."  !");
            }
        }
    }

    private function validType(string $type, mixed $value): bool
    {
        return match ($type) {
            'string' => is_string($value),
            'bool' => is_bool($value),
            'int' => is_int($value),
        };
    }

    abstract function jsonSchema(): array;

    abstract function defaultValue(): array;

}