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

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\Variables;

/**
 * Tests for the \PHPCSUtils\Utils\Variables::getAttributeOpeners method.
 *
 * @covers \PHPCSUtils\Utils\Variables::getAttributeOpeners
 * @covers \PHPCSUtils\Internal\AttributeHelper::getOpeners
 *
 * @group attributes
 * @group variables
 *
 * @since 1.2.0
 */
final class GetOpenersForVariablesParseError1Test extends PolyfilledTestCase
{

    /**
     * Test receiving an expected exception when a variable token for a property with a parse error is passed.
     *
     * @return void
     */
    public function testNotPropertyOrParamException()
    {
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage(
            'argument #2 ($stackPtr) must be the pointer to an OO property or a parameter in a function declaration.'
        );

        $targetPtr = $this->getTargetToken('/* testParseError */', \T_VARIABLE);
        Variables::getAttributeOpeners(self::$phpcsFile, $targetPtr);
    }
}
