<?php

namespace AzureOSS\Storage\Common\Internal\Serialization;

use AzureOSS\Storage\Common\Internal\Resources;
use AzureOSS\Storage\Common\Internal\Utilities;
use AzureOSS\Storage\Common\Internal\Validate;

class XmlSerializer implements ISerializer
{
    public const STANDALONE = 'standalone';
    public const ROOT_NAME = 'rootName';
    public const DEFAULT_TAG = 'defaultTag';

    /**
     * Converts a SimpleXML object to an Array recursively
     * ensuring all sub-elements are arrays as well.
     *
     * @param string $sxml The SimpleXML object.
     * @param array|null $arr  The array into which to store results.
     *
     * @return array
     */
    private function sxml2arr($sxml, ?array $arr = null)
    {
        foreach ((array) $sxml as $key => $value) {
            if (is_object($value) || (is_array($value))) {
                $arr[$key] = $this->sxml2arr($value);
            } else {
                $arr[$key] = $value;
            }
        }

        return $arr;
    }

    /**
     * Takes an array and produces XML based on it.
     *
     * @param \XMLWriter $xmlw XMLWriter object that was previously instanted
     *                              and is used for creating the XML.
     * @param array $data Array to be converted to XML.
     * @param null $defaultTag Default XML tag to be used if none specified.
     */
    private function arr2xml(\XMLWriter $xmlw, array $data, $defaultTag = null)
    {
        foreach ($data as $key => $value) {
            if ($key === Resources::XTAG_ATTRIBUTES) {
                foreach ($value as $attributeName => $attributeValue) {
                    $xmlw->writeAttribute($attributeName, $attributeValue);
                }
            } elseif (is_array($value)) {
                if (!is_int($key)) {
                    if ($key != Resources::EMPTY_STRING) {
                        $xmlw->startElement($key);
                    } else {
                        $xmlw->startElement($defaultTag);
                    }
                }

                $this->arr2xml($xmlw, $value);

                if (!is_int($key)) {
                    $xmlw->endElement();
                }
            } else {
                $xmlw->writeElement($key, $value);
            }
        }
    }

    /**
     * Gets the attributes of a specified object if get attributes
     * method is exposed.
     *
     * @param object $targetObject The target object.
     * @param array  $methodArray  The array of method of the target object.
     */
    private static function getInstanceAttributes($targetObject, array $methodArray)
    {
        foreach ($methodArray as $method) {
            if ($method->name == 'getAttributes') {
                return $method->invoke($targetObject);
            }
        }
        return null;
    }

    /**
     * Serialize an object with specified root element name.
     *
     * @param object $targetObject The target object.
     * @param string $rootName     The name of the root element.
     *
     * @return string
     */
    public static function objectSerialize($targetObject, $rootName)
    {
        Validate::notNull($targetObject, 'targetObject');
        Validate::canCastAsString($rootName, 'rootName');
        $xmlWriter = new \XmlWriter();
        $xmlWriter->openMemory();
        $xmlWriter->setIndent(true);
        $reflectionClass = new \ReflectionClass($targetObject);
        $methodArray = $reflectionClass->getMethods();
        $attributes = self::getInstanceAttributes(
            $targetObject,
            $methodArray,
        );

        $xmlWriter->startElement($rootName);
        if (null !== $attributes) {
            foreach (array_keys($attributes) as $attributeKey) {
                $xmlWriter->writeAttribute(
                    $attributeKey,
                    $attributes[$attributeKey],
                );
            }
        }

        foreach ($methodArray as $method) {
            if ((strpos($method->name, 'get') === 0)
                && $method->isPublic()
                && ($method->name != 'getAttributes')
            ) {
                $variableName = substr($method->name, 3);
                $variableValue = $method->invoke($targetObject);
                if (!empty($variableValue)) {
                    if (gettype($variableValue) === 'object') {
                        $xmlWriter->writeRaw(
                            XmlSerializer::objectSerialize(
                                $variableValue,
                                $variableName,
                            ),
                        );
                    } else {
                        $xmlWriter->writeElement($variableName, $variableValue);
                    }
                }
            }
        }
        $xmlWriter->endElement();
        return $xmlWriter->outputMemory(true);
    }

    /**
     * Serializes given array. The array indices must be string to use them as
     * as element name.
     *
     * @param array $array      The object to serialize represented in array.
     * @param array|null $properties The used properties in the serialization process.
     *
     * @return string
     */
    public function serialize(array $array, ?array $properties = null)
    {
        $xmlVersion = '1.0';
        $xmlEncoding = 'UTF-8';
        $standalone = Utilities::tryGetValue($properties, self::STANDALONE);
        $defaultTag = Utilities::tryGetValue($properties, self::DEFAULT_TAG);
        $rootName = Utilities::tryGetValue($properties, self::ROOT_NAME);
        $docNamespace = Utilities::tryGetValue(
            $array,
            Resources::XTAG_NAMESPACE,
            null,
        );

        if (!is_array($array)) {
            return false;
        }

        $xmlw = new \XmlWriter();
        $xmlw->openMemory();
        $xmlw->setIndent(true);
        $xmlw->startDocument($xmlVersion, $xmlEncoding, $standalone);

        if (null === $docNamespace) {
            $xmlw->startElement($rootName);
        } else {
            foreach ($docNamespace as $uri => $prefix) {
                $xmlw->startElementNS($prefix, $rootName, $uri);
                break;
            }
        }

        unset($array[Resources::XTAG_NAMESPACE]);
        self::arr2xml($xmlw, $array, $defaultTag);

        $xmlw->endElement();

        return $xmlw->outputMemory(true);
    }

    /**
     * Unserializes given serialized string.
     *
     * @param string $serialized The serialized object in string representation.
     *
     * @return array
     */
    public function unserialize($serialized)
    {
        $sxml = new \SimpleXMLElement($serialized);

        return $this->sxml2arr($sxml);
    }
}
