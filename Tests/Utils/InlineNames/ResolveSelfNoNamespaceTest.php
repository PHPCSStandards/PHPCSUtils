<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\InlineNames;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\InlineNames;

/**
 * Tests for the \PHPCSUtils\Utils\InlineNames::resolveSelf() method.
 *
 * @covers \PHPCSUtils\Utils\InlineNames::resolveSelf
 *
 * @group inlinenames
 *
 * @since 1.0.0
 */
class ResolveSelfNoNamespaceTest extends UtilityMethodTestCase
{

    /**
     * Test resolving a T_SELF token to the fully qualified name of the current class/interface/trait
     * in a file without a namespace declaration.
     *
     * @return void
     */
    public function testResolveSelf()
    {
        $stackPtr = $this->getTargetToken('/* testGlobalClass */', \T_SELF);
        $result   = InlineNames::resolveSelf(self::$phpcsFile, $stackPtr);
        $this->assertSame('\Foo', $result);
    }
}
