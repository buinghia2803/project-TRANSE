<?php

namespace App\Services\XMLProcedures;

abstract class BaseClsProcedure
{
    protected $hasCollectAttribute = false;

    /**
     * Initializing the instances and variables
     *
     * @param array $data;
     */
    public function __construct($data = [])
    {
        $this->fill($data);
    }

    /**
     * Fill attributes in class
     * @param array $attributes;
     */
    function fill($attributes = [])
    {
        try {
            foreach ($attributes as $key => $value) {
                $keyRep = str_replace('-', '_',  $key);
                $clsName = $this->dashesToClassPath($key);
                if(is_array($value) && $keyRep != '@attributes') {
                    if(class_exists($clsName)) {
                        $class = (new $clsName());
                        if($class->hasCollectAttribute) {
                            $this->bindCollectionClass($class, $value);
                            $this->{$keyRep} = $class;
                        } else {
                            $this->{$keyRep} = new $clsName($value);
                        }
                    } else {
                        foreach ($value as $sKey => $val) {
                            if ($sKey == '@attributes') {
                                foreach ($val as $keyAttr => $valueAttr) {
                                    $this->{$keyRep}[str_replace('-', '_', $keyAttr)] = $valueAttr;
                                }
                            } else {
                                $this->{$keyRep}[str_replace('-', '_', $sKey)] = $val;
                            }
                        }
                    }
                } else if($keyRep == '@attributes') {
                    foreach ($attributes['@attributes'] as $keyAttr => $valueAttr) {
                        $this->{str_replace('-', '_',  $keyAttr)} = $valueAttr;
                    }
                } else {
                    $this->{$keyRep} = $value;
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Binding data with collection class.
     * @param Class $class
     * @param array $value
     */
    protected function bindCollectionClass(&$class, $data)
    {
        foreach ($data as $key => $properties) {
            if($key == '@attributes') {
                foreach ($data['@attributes'] as $attrKey => $attrValue) {
                    $class->{$attrKey} = $attrValue;
                }
            } else {
                $keyRep = str_replace('-', '_',  $key);
                $clsName = $this->dashesToClassPath($key);
                if((class_exists($clsName))) {
                    $class->{$keyRep} = collect([]);
                    if(isset($properties['@attributes'])) {
                        $class->{$keyRep}->push(new $clsName($properties));
                    } else {
                        foreach ($properties as $pKey => $value) {
                            $class->{$keyRep}->push(new $clsName($value));
                        }
                    }
                } else {
                    $class->{$keyRep} = $properties;
                }

            }
        }
    }

    /**
     * Convert string to uppercase first word.
     * @param string $string
     * @param bool $capitalizeFirstCharacter
     * @return string
     */
    protected function dashesToClassPath(string $string, bool $capitalizeFirstCharacter = true)
    {

        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }

        return __NAMESPACE__ . '\\' . $str;
    }
}