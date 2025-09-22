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
final class AppliesToParseError2Test extends UtilityMethodTestCase
{

    /**
     * Verify that `false` is returned if the construct the attribute applies to cannot be determined.
     *
     * @return void
     */
    public function testAttributeWithoutTarget()
    {
        $targetPtr = $this->getTargetToken('/* testAttributeWithoutTarget */', \T_ATTRIBUTE);
        $result    = AttributeBlock::appliesTo(self::$phpcsFile, $targetPtr);

        $this->assertFalse($result);
    }
}
