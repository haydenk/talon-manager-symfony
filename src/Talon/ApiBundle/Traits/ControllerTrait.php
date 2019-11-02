<?php

namespace Talon\ApiBundle\Traits;

trait ControllerTrait
{
    protected function convertToDoctrineOrderBy($sort)
    {
        $fields = [];

        foreach (explode(',', $sort) as $key => $field) {

            list($fieldName, $fieldValue) = array_pad(explode(':', $field, 2), 2, 'asc');

            if (null === $fieldName || strlen(trim($fieldName)) === 0) {
                continue;
            }

            $fields[$fieldName] = in_array(strtolower($fieldValue), ['asc', 'desc']) ? strtolower($fieldValue) : 'asc';

        }

        return $fields;
    }

    protected function parseParametersToCriteria(array $parameters, array $ignoreParameters = ['page', 'limit', 'sort', 'csv'])
    {
        $criteria = [];

        foreach ($parameters as $key => $value) {

            if (null === $value) {
                continue;
            }

            if (in_array($key, $ignoreParameters)) {
                continue;
            }

            $criteriaKey = $this->hyphenToCamelCase($key);
            $criteria[$criteriaKey] = $value;
        }

        return $criteria;
    }

    protected function hyphenToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = ucwords($string, '-');
        $str = str_replace('-', '', $str);

        if (false === $capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }
}