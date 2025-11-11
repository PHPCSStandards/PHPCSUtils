<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2025 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\AttributeBlock;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\AttributeBlock;

/**
 * Test for the \PHPCSUtils\Utils\AttributeBlock::appliesTo() method.
 *
 * @covers \PHPCSUtils\Utils\AttributeBlock::appliesTo
 *
 * @group attributes
 *
 * @since 1.2.0
 */
final class AppliesToParseError6Test extends UtilityMethodTestCase
{

    /**
     * Document parse error tolerance.
     *
     * When attributes are used in invalid context or keywords which can exist between the attribute
     * and the target construct are used incorrectly, the utility method will still find the
     * target construct, even though the code under scan would amount to invalid PHP.
     *
     * @return void
     */
    public function testTargetGetsFoundForInvalidPhp()
    {
        $targetPtr   = $this->getTargetToken('/* testInvalidPHP */', \T_ATTRIBUTE);
        $expectedPtr = $this->getTargetToken('/* testInvalidPHP */', \T_VARIABLE);
        $result      = AttributeBlock::appliesTo(self::$phpcsFile, $targetPtr);

        $this->assertSame($expectedPtr, $result);
    }
}
