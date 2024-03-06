<?php

namespace App\DTO;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionProperty;

/**
 * Базовый класс DTO
 */
abstract class BaseDTO implements DTO
{
    /**
     * Вернуть все свойства объекта в виде ассоциативного массива
     * @return array
     */
    public function toArray(): array
    {
        $properties = [];
        $reflection = new ReflectionClass($this);
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $propertyName = $property->getName();
            $properties[$propertyName] = $this->$propertyName;
        }

        return $properties;
    }

    /**
     * Заполнить свойства объекта из массива
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $reflection = new ReflectionClass(static::class);
        $constructor = $reflection->getConstructor();
        if ($constructor !== null) {
            $parameters = $constructor->getParameters();
            $args = [];
            foreach ($parameters as $parameter) {
                $name = $parameter->getName();
                if (!array_key_exists($name, $data)) {
                    if ($parameter->isDefaultValueAvailable()) {
                        $args[] = $parameter->getDefaultValue();
                    } else {
                        throw new InvalidArgumentException("Отсутствует необходимый параметр: {$name}");
                    }
                } else {
                    $args[] = $data[$name];
                }
            }
            $instance = new static(...$args);
        } else {
            $instance = new static();
        }

        // необязательные параметры
        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }

        return $instance;
    }
}
