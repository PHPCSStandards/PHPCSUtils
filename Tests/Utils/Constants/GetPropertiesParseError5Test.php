<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Constants;

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\Constants;

/**
 * Tests for the \PHPCSUtils\Utils\Constants::getProperties method.
 *
 * @covers \PHPCSUtils\Utils\Constants::getProperties
 *
 * @group constants
 *
 * @since 1.1.0
 */
final class GetPropertiesParseError5Test extends PolyfilledTestCase
{

    /**
     * Test receiving an exception when encountering a specific parse error.
     *
     * @return void
     */
    public function testParseError()
    {
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage('The value of argument #2 ($stackPtr) must be the pointer to an OO constant');

        $const = $this->getTargetToken('/* testParseErrorLiveCoding */', \T_CONST);
        Constants::getProperties(self::$phpcsFile, $const);
    }
}
