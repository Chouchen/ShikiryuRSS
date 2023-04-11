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
    protected ?object $object = null;
    /**
     * @throws ReflectionException
     */
    public function isPropertyValid($object, $property)
    {
        $properties = array_filter($this->_getClassProperties(get_class($object)), fn($p) => $p->getName() === $property);
        if (count($properties) !== 1) {
            return false;
        }

        $properties = current($properties);
        $propertyValue = $object->{$properties->name};
        $propertyAnnotations = $this->_getPropertyAnnotations($properties);

        if (!in_array('required', $propertyAnnotations, true) && empty($propertyValue)) {
            return true;
        }

        foreach ($propertyAnnotations as $propertyAnnotation) {
            $annotation = explode(' ', $propertyAnnotation);

            $object->validated[$properties->name] = $this->_validateProperty($annotation, $propertyValue);
        }

        return false;
    }

    /**
     * @throws ReflectionException
     */
    public function isObjectValid($object): bool
    {
        if (!$object->validated) {
            $object = $this->validateObject($object);
        }

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
//            $propertyAnnotations = $this->_getPropertyAnnotations($property, get_class($object));

            if (!in_array('required', $property['rules'], true) && empty($propertyValue)) {
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

        return $this->{sprintf('_validate%s', ucfirst($annotation[0]))}($property, ...$args_annotation);
    }

    /**
     * @return ReflectionProperty[]
     * @throws ReflectionException
     */
    private function _getClassProperties($class): array
    {
        return (new ReflectionClass($class))->getProperties();
    }

    private function _getPropertyAnnotations(ReflectionProperty $property): array
    {
        preg_match_all('#@(.*?)\n#s', $property->getDocComment(), $annotations);

        return array_map(fn($annotation) => trim($annotation), $annotations[1]);
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
        $options = [
            'options' => [
                'default' => 0,
                'min_range' => 0,
                'max_range' => 23
            ]
        ];
        return filter_var($value, FILTER_VALIDATE_INT, $options) !== false;
    }

    private function _validateDay($value): bool
    {
        return in_array(
            strtolower($value),
            ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']
        );
    }

    private function _validateContentMedia($value)
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
}