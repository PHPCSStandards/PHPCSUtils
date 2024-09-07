<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
 * @phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound
 */

namespace PHPCSUtils\Tests;

use PHPCSUtils\Tests\AssertPropertySame;
use PHPCSUtils\Tests\ExpectWithConsecutiveArgs;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use Yoast\PHPUnitPolyfills\Autoload;
use Yoast\PHPUnitPolyfills\Polyfills\AssertArrayWithListKeys;
use Yoast\PHPUnitPolyfills\Polyfills\AssertClosedResource;
use Yoast\PHPUnitPolyfills\Polyfills\AssertEqualsSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\AssertFileDirectory;
use Yoast\PHPUnitPolyfills\Polyfills\AssertFileEqualsSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\AssertIgnoringLineEndings;
use Yoast\PHPUnitPolyfills\Polyfills\AssertionRenames;
use Yoast\PHPUnitPolyfills\Polyfills\AssertIsList;
use Yoast\PHPUnitPolyfills\Polyfills\AssertIsType;
use Yoast\PHPUnitPolyfills\Polyfills\AssertNumericType;
use Yoast\PHPUnitPolyfills\Polyfills\AssertObjectEquals;
use Yoast\PHPUnitPolyfills\Polyfills\AssertObjectNotEquals;
use Yoast\PHPUnitPolyfills\Polyfills\AssertObjectProperty;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;
use Yoast\PHPUnitPolyfills\Polyfills\EqualToSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectException;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectExceptionMessageMatches;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectExceptionObject;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectPHPException;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectUserDeprecation;

if (\version_compare(Autoload::VERSION, '3.0.0', '>=')) {
    /**
     * Abstract utility method base test case which includes all available polyfills (PHPUnit Polyfills 3.x compatible).
     *
     * This test case includes all polyfills from the PHPUnit Polyfill library to make them
     * available to the tests.
     *
     * Generally speaking, this testcase only needs to be used when the concrete test class will
     * use functionality which has changed in PHPUnit cross-version.
     * In all other cases, the `UtilityMethodTestCase` can be extended directly.
     *
     * {@internal The list of included polyfill traits should be reviewed after each new
     * release of the PHPUnit Polyfill library.}
     *
     * @since 1.1.0
     */
    abstract class PolyfilledTestCase extends UtilityMethodTestCase
    {
        // PHPCSUtils native helpers.
        use AssertPropertySame;
        use ExpectWithConsecutiveArgs;

        // PHPUnit Polyfills.
        use AssertArrayWithListKeys;
        use AssertClosedResource;
        use AssertEqualsSpecializations;
        use AssertFileEqualsSpecializations;
        use AssertIgnoringLineEndings;
        use AssertionRenames;
        use AssertIsList;
        use AssertIsType;
        use AssertObjectEquals;
        use AssertObjectNotEquals;
        use AssertObjectProperty;
        use AssertStringContains;
        use EqualToSpecializations;
        use ExpectExceptionMessageMatches;
        use ExpectUserDeprecation;
    }
} elseif (\version_compare(Autoload::VERSION, '2.0.0', '>=')) {
    /**
     * Abstract utility method base test case which includes all available polyfills (PHPUnit Polyfills 2.x compatible).
     *
     * This test case includes all polyfills from the PHPUnit Polyfill library to make them
     * available to the tests.
     *
     * Generally speaking, this testcase only needs to be used when the concrete test class will
     * use functionality which has changed in PHPUnit cross-version.
     * In all other cases, the `UtilityMethodTestCase` can be extended directly.
     *
     * {@internal The list of included polyfill traits should be reviewed after each new
     * release of the PHPUnit Polyfill library.}
     *
     * @since 1.0.0
     */
    abstract class PolyfilledTestCase extends UtilityMethodTestCase
    {
        // PHPCSUtils native helpers.
        use AssertPropertySame;
        use ExpectWithConsecutiveArgs;

        // PHPUnit Polyfills.
        use AssertClosedResource;
        use AssertEqualsSpecializations;
        use AssertFileEqualsSpecializations;
        use AssertIgnoringLineEndings;
        use AssertionRenames;
        use AssertIsList;
        use AssertIsType;
        use AssertObjectEquals;
        use AssertObjectProperty;
        use AssertStringContains;
        use EqualToSpecializations;
        use ExpectExceptionMessageMatches;
        use ExpectExceptionObject;
    }
} else {
    /**
     * Abstract utility method base test case which includes all available polyfills (PHPUnit Polyfills 1.x compatible).
     *
     * This test case includes all polyfills from the PHPUnit Polyfill library to make them
     * available to the tests.
     *
     * Generally speaking, this testcase only needs to be used when the concrete test class will
     * use functionality which has changed in PHPUnit cross-version.
     * In all other cases, the `UtilityMethodTestCase` can be extended directly.
     *
     * {@internal The list of included polyfill traits should be reviewed after each new
     * release of the PHPUnit Polyfill library.}
     *
     * @since 1.0.0
     */
    abstract class PolyfilledTestCase extends UtilityMethodTestCase
    {
        // PHPCSUtils native helper.
        use AssertPropertySame;
        use ExpectWithConsecutiveArgs;

        // PHPUnit Polyfills.
        use AssertClosedResource;
        use AssertEqualsSpecializations;
        use AssertFileDirectory;
        use AssertFileEqualsSpecializations;
        use AssertionRenames;
        use AssertIsType;
        use AssertNumericType;
        use AssertObjectEquals;
        use AssertObjectProperty;
        use AssertStringContains;
        use EqualToSpecializations;
        use ExpectException;
        use ExpectExceptionMessageMatches;
        use ExpectExceptionObject;
        use ExpectPHPException;
    }
}
