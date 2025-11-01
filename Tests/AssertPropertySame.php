<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests;

use Exception;
use ReflectionException;
use ReflectionObject;

/**
 * PHPUnit cross-version compatibility helper.
 *
 * Backfills the PHPUnit native `assertAttributeSame()` method in PHPUnit 9.x and above in which
 * the method was removed. Use `assertPropertySame()` instead of `assertAttributeSame()`
 * for cross-version compatibility.
 *
 * @since 1.0.0
 */
trait AssertPropertySame
{

    /**
     * PHPUnit cross-version helper method to test the value of class properties.
     *
     * @param mixed  $expected     Expected property value.
     * @param string $propertyName The name of the property to check.
     * @param object $actualObject The object on which to check the property value.
     * @param string $message      Optional. Custom error message.
     *
     * @return void
     */
    final public function assertPropertySame($expected, $propertyName, $actualObject, $message = '')
    {
        // Will throw a warning on PHPUnit 8, but will still work.
        if (\method_exists($this, 'assertAttributeSame')) {
            $this->assertAttributeSame($expected, $propertyName, $actualObject, $message);
            return;
        }

        // PHPUnit 9.0+.
        try {
            $actual = $this->getObjectPropertyValue($actualObject, $propertyName);
        } catch (Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertSame($expected, $actual, $message);
    }

    /**
     * Retrieve the value of an object property.
     *
     * This also works for properties that are declared protected or private.
     *
     * @param object|string $objectUnderTest The object or class on which to check the property value.
     * @param string        $propertyName    The name of the property to check.
     *
     * @return mixed Property value.
     *
     * @throws \Exception
     */
    final public static function getObjectPropertyValue($objectUnderTest, $propertyName)
    {
        $reflector = new ReflectionObject($objectUnderTest);

        do {
            try {
                $property = $reflector->getProperty($propertyName);

                // phpcs:ignore Squiz.Operators.ComparisonOperatorUsage.NotAllowed -- This is deliberate to mirror the PHPUnit method.
                if (!$property || $property->isPublic()) {
                    return $objectUnderTest->$propertyName;
                }

                (\PHP_VERSION_ID < 80100) && $property->setAccessible(true);
                $value = $property->getValue($objectUnderTest);
                (\PHP_VERSION_ID < 80100) && $property->setAccessible(false);

                return $value;
            } catch (ReflectionException $e) {
            }
        } while ($reflector = $reflector->getParentClass());

        throw new Exception(
            \sprintf(
                'Property "%s" not found in object.',
                $propertyName
            )
        );
    }
}
