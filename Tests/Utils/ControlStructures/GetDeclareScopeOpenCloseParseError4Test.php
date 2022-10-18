<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ControlStructures;

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\ControlStructures;

/**
 * Tests for the \PHPCSUtils\Utils\ControlStructures::getDeclareScopeOpenClose() method.
 *
 * @covers \PHPCSUtils\Utils\ControlStructures::getDeclareScopeOpenClose
 *
 * @group controlstructures
 *
 * @since 1.0.0
 */
final class GetDeclareScopeOpenCloseParseError4Test extends PolyfilledTestCase
{

    /**
     * Test that the function returns `false` in the case of a particular parse error.
     *
     * @return void
     */
    public function testGetDeclareScopeOpenCloseParseError()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessage(
            'ControlStructures::getDeclareScopeOpenClose() function is deprecated since PHPCSUtils 1.0.0-alpha4.'
            . ' Check for the "scope_opener"/"scope_closer" keys instead.'
        );

        $stackPtr = $this->getTargetToken('/* testUnexpectedToken */', \T_DECLARE);
        $result   = ControlStructures::getDeclareScopeOpenClose(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);
    }
}
