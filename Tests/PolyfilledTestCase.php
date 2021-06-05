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

use PHPCSUtils\Tests\AssertAttributeSame;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use Yoast\PHPUnitPolyfills\Polyfills\AssertClosedResource;
use Yoast\PHPUnitPolyfills\Polyfills\AssertEqualsSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\AssertFileDirectory;
use Yoast\PHPUnitPolyfills\Polyfills\AssertFileEqualsSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\AssertionRenames;
use Yoast\PHPUnitPolyfills\Polyfills\AssertIsType;
use Yoast\PHPUnitPolyfills\Polyfills\AssertNumericType;
use Yoast\PHPUnitPolyfills\Polyfills\AssertObjectEquals;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;
use Yoast\PHPUnitPolyfills\Polyfills\EqualToSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectException;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectExceptionMessageMatches;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectExceptionObject;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectPHPException;

/**
 * Abstract utilty method base test case which includes all available polyfills.
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
    use AssertAttributeSame;

    // PHPUnit Polyfills.
    use AssertClosedResource;
    use AssertEqualsSpecializations;
    use AssertFileDirectory;
    use AssertFileEqualsSpecializations;
    use AssertionRenames;
    use AssertIsType;
    use AssertNumericType;
    use AssertObjectEquals;
    use AssertStringContains;
    use EqualToSpecializations;
    use ExpectException;
    use ExpectExceptionMessageMatches;
    use ExpectExceptionObject;
    use ExpectPHPException;
}
