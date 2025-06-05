<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getMethodParameters method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getMethodParameters
 *
 * @group functiondeclarations
 *
 * @since 1.1.0
 */
final class GetMethodParametersParseError3Test extends UtilityMethodTestCase
{

    /**
     * Test receiving an exception when encountering a specific parse error.
     *
     * @return void
     */
    public function testParseError()
    {
        $this->expectPhpcsException('$stackPtr was not a valid T_USE');

        $target = $this->getTargetToken('/* testParseError */', [\T_USE]);
        BCFile::getMethodParameters(self::$phpcsFile, $target);
    }
}
