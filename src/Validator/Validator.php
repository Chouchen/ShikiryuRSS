<?php

namespace Shikiryu\SRSS\Validator;

use DateTime;
use DateTimeInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Shikiryu\SRSS\Entity\Media\Content;

class Validator
{
    use ReadProperties;

    protected ?object $object = null;


    /**
     * @param $object
     * @param $property
     * @param $value
     * @return bool
     */
    public function isValidValueForObjectProperty($object, $property, $value): bool
    {
        $this->object = $object;
        try {
            $property = $this->getReflectedProperty($object, $property);
        } catch (ReflectionException) {
            return false;
        }
        $propertyAnnotations = $this->_getPropertyAnnotations($property);

        if (empty($value) && count(array_filter($propertyAnnotations, static fn($rule) => str_starts_with($rule, 'required'))) === 0) {
            return true;
        }

        foreach ($propertyAnnotations as $propertyAnnotation) {
            $annotation = explode(' ', $propertyAnnotation);

            if ($this->_validateProperty($annotation, $value) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $object
     * @param $property
     * @return bool
     * @throws ReflectionException
     */
    private function objectHasProperty($object, $property): bool
    {
        return $this->getReflectedProperty($object, $property) instanceof ReflectionProperty;
    }

    /**
     * @throws ReflectionException
     */
    public function isPropertyValid($object, $property): bool
    {
        return $this->objectHasProperty($object, $property) &&
            $this->isValidValueForObjectProperty($object, $property, $object->{$property});
    }

    /**
     * @throws ReflectionException
     */
    public function isObjectValid($object): bool
    {
        $object->validated = [];
//        if (!$object->validated) {
            $object = $this->validateObject($object);
//        }

        return !in_array(false, $object->validated, true);
    }

    /**
     * @throws ReflectionException
     */
    public function validateObject($object)
    {
        $this->object = $object;
        $properties = $this->_getClassProperties(get_class($object));
        $properties = array_map(fn($property) => array_merge(
            ['name' => $property->name],
            ['rules' => $this->_getPropertyAnnotations($property)]
        ), $properties);

        foreach ($properties as $property) {
            $propertyValue = $object->{$property['name']};

            if (empty($propertyValue) && count(array_filter($property['rules'], static fn($rule) => str_starts_with($rule, 'required'))) === 0) {
                continue;
            }

            foreach ($property['rules'] as $propertyAnnotation) {
                $annotation = explode(' ', $propertyAnnotation);

                $object->validated[$property['name']] = $this->_validateProperty($annotation, $propertyValue);
            }
        }

        return $object;
    }

    private function _validateProperty(array $annotation, $property): bool
    {
        if ($annotation[0] === 'var') {
            return true;
        }

        if (count($annotation) === 1) {
            return $this->{sprintf('_validate%s', ucfirst($annotation[0]))}($property);
        }

        $args_annotation = array_splice($annotation, 1);

        return $this->{sprintf('_validate%s', ucfirst($annotation[0]))}($property, $args_annotation);
    }


    private function _getPropertyAnnotations(ReflectionProperty $property): array
    {
        preg_match_all('#@validate (.*?)\n#s', $property->getDocComment(), $annotations);

        return array_map(static fn($annotation) => trim($annotation), $annotations[1]);
    }

    private function _validateString($value): bool
    {
        return is_string($value);
    }

    private function _validateInt($value): bool
    {
        return is_numeric($value);
    }

    private function _validateRequired($value): bool
    {
        return !empty(trim($value));
    }

    private function _validateRequiredOr($value, $other_values): bool
    {
        if (!empty($value)) {
            return true;
        }

        foreach ($other_values as $other_value) {
            if (!empty($this->object->$other_value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $value
     * @return bool
     * https://cyber.harvard.edu/rss/languages.html
     */
    private function _validateLang($value): bool
    {
        return in_array(strtolower($value), [
            'af','sq','eu','be','bg','ca','zh-cn','zh-tw','hr','cs','da','nl','nl-be','nl-nl','en','en-au','en-bz',
            'en-ca','en-ie','en-jm','en-nz','en-ph','en-za','en-tt','en-gb','en-us','en-zw','et','fo','fi','fr','fr-be',
            'fr-ca','fr-fr','fr-lu','fr-mc','fr-ch','gl','gd','de','de-at','de-de','de-li','de-lu','de-ch','el','haw',
            'hu','is','in','ga','it','it-it','it-ch','ja','ko','mk','no','pl','pt','pt-br','pt-pt','ro','ro-mo','ro-ro',
            'ru','ru-mo','ru-ru','sr','sk','sl','es','es-ar','es-bo','es-cl','es-co','es-cr','es-do','es-ec','es-sv',
            'es-gt','es-hn','es-mx','es-ni','es-pa','es-py','es-pe','es-pr','es-es','es-uy','es-ve','sv','sv-fi','sv-se',
            'tr','uk',
        ]);
    }

    /**
     * @param $value
     * @return bool
     */
    private function _validateNoHtml($value): bool
    {
        return strip_tags($value) === $value;
    }

    private function _validateUrl($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }

    private function _validateDate($value): bool
    {
        return DateTime::createFromFormat(DateTimeInterface::RSS, $value) !== false;
    }

    private function _validateHour($value): bool
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                if ($this->_validateHour($val) === false) {
                    return false;
                }
            }

            return true;
        }

        $options = [
            'options' => [
                'min_range' => 0,
                'max_range' => 23
            ]
        ];

        return filter_var($value, FILTER_VALIDATE_INT, $options) !== false;
    }

    private function _validateDay($value): bool
    {
        if (is_array($value)) {
            foreach ($value as $val) {
                if ($this->_validateDay($val) === false) {
                    return false;
                }
            }

            return true;
        }

        return in_array(
            strtolower($value),
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
        );
    }

    private function _validateContentMedia($value): bool
    {
        if (is_array($value)) {
            foreach ($value as $content) {
                if (!$content->isValid()) {
                    return false;
                }
            }
            return true;
        }

        if ($value instanceof Content) {
            return $value->isValid();
        }

        return  false;
    }

    private function _validateMediaType($value): bool
    {
        return true;
    }
    private function _validateMediaMedium($value): bool
    {
        return in_array($value, ['image', 'audio', 'video', 'document', 'executable']);
    }

    private function _validateMediaExpression($value): bool
    {
        return in_array($value, ['sample', 'full', 'nonstop']);
    }

    private function _validateEmail($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    private function _validateMax($value, array $max): bool
    {
        return $value <= current($max);
    }
    private function _validateMin($value, array $max): bool
    {
        return $value >= current($max);
    }
}