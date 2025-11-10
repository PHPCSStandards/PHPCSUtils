<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2025 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\AttributeHelper;

use PHPCSUtils\Internal\AttributeHelper;
use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Tests for the \PHPCSUtils\Internal\AttributeHelper::getOpeners method.
 *
 * @covers \PHPCSUtils\Internal\AttributeHelper::getOpeners
 *
 * @group attributes
 *
 * @since 1.2.0
 */
final class GetOpenersMiscTest extends PolyfilledTestCase
{

    /**
     * Test receiving an exception when passing a non-string "type".
     *
     * @return void
     */
    public function testNonStringType()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #3 ($type) must be of type string, boolean given.');

        AttributeHelper::getOpeners(self::$phpcsFile, 2, false);
    }

    /**
     * Test receiving an exception when the passed "type" is not one of the recognized ones.
     *
     * @return void
     */
    public function testInvalidType()
    {
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage(
            'The value of argument #3 ($type) must be one of the following: constant, function, OO, variable.'
        );

        AttributeHelper::getOpeners(self::$phpcsFile, 2, 'invalid');
    }
}
