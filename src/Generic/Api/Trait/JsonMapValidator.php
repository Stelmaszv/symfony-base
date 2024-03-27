<?php


namespace App\Generic\Api\Trait;

use App\Generic\Components\AbstractJsonMapper;

trait JsonMapValidator
{
	private function jsonValidate(?array $value, string $mapper) :array
	{
		
		$mapperObj = new $mapper();

		if(!$mapperObj instanceof AbstractJsonMapper){
			throw new \InvalidArgumentException("Invalid Instance !");
		}

		if($value === null){
			return $mapperObj->defaultValue();
		}

		if(count($value) === 0){
			return $mapperObj->defaultValue();
		}

		if($mapperObj->isValid($value)){
			return $value;
		}

		return $mapperObj->defaultValue();
	}
}