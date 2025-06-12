<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\UseStatements;

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\UseStatements;

/**
 * Test for the \PHPCSUtils\Utils\UseStatements::getType() methods.
 *
 * @covers \PHPCSUtils\Utils\UseStatements::getType
 *
 * @since 1.1.0
 */
final class UseTypeParseError2Test extends PolyfilledTestCase
{

    /**
     * Test that a `use` keyword for which the type cannot be determined returns an empty string.
     *
     * @return void
     */
    public function testUndeterminedUse()
    {
        $stackPtr = $this->getTargetToken('/* testLiveCoding */', \T_USE);

        $result = UseStatements::getType(self::$phpcsFile, $stackPtr);
        $this->assertSame('', $result);
    }
}
