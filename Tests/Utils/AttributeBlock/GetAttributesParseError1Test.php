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
 * Test for the \PHPCSUtils\Utils\AttributeBlock::getAttributes() method.
 *
 * @covers \PHPCSUtils\Utils\AttributeBlock::getAttributes
 *
 * @group attributes
 *
 * @since 1.2.0
 */
final class GetAttributesParseError1Test extends UtilityMethodTestCase
{

    /**
     * Test that an empty array is returned when an attribute block is unfinished.
     *
     * @return void
     */
    public function testUnfinishedAttribute()
    {
        $targetPtr = $this->getTargetToken('/* testLiveCoding */', \T_ATTRIBUTE);
        $result    = AttributeBlock::getAttributes(self::$phpcsFile, $targetPtr);

        $this->assertSame([], $result);
    }
}
