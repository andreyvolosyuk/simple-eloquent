<?php

namespace Volosyuk\SimpleEloquent;

use Exception;
use stdClass;

/**
 * Class ModelAccessor
 * @package App\Eloomi
 */
class ModelAccessor
{
    /**
     * @param $model
     * @param $attribute
     * @param $value
     */
    public static function set(&$model, $attribute, $value)
    {
        if (is_array($model)) {
            $model[$attribute] = $value;
        } elseif (is_object($model)) {
            $model->{$attribute} = $value;
        }
    }

    /**
     * @param $model
     * @param $attribute
     * @return mixed|null
     */
    public static function get($model, $attribute)
    {
        if (is_array($model)) {
            return $model[$attribute];
        } elseif (is_object($model)) {
            return $model->{$attribute};
        }

        return null;
    }

    /**
     * @param $model
     * @param $attribute
     */
    public static function delete(&$model, $attribute)
    {
        if (is_array($model)) {
            unset($model[$attribute]);
        } elseif (is_object($model)) {
            unset($model->{$attribute});
        }
    }

    /**
     * @param $model
     * @param $attribute
     * @return bool
     */
    public static function exists($model, $attribute)
    {
        if (is_array($model)) {
            return isset($model[$attribute]);
        } elseif (is_object($model)) {
            return property_exists($model, $attribute);
        }

        return false;
    }

    /**
     * @param $model
     * @return array|stdClass
     * @throws Exception
     */
    public static function createBasedOnModel($model)
    {
        if (is_array($model)) {
            return self::create(true);
        } elseif (is_object($model)) {
            return self::create(false);
        }

        throw new Exception('Model type is not valid');
    }

    /**
     * @param bool $buildArray
     * @return array|stdClass
     */
    private static function create($buildArray = true)
    {
        if ($buildArray) {
            return [];
        }

        return new stdClass;
    }
}
