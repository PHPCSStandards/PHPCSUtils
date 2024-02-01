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

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
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
final class GetPropertiesParseError1Test extends UtilityMethodTestCase
{

    /**
     * Test receiving an exception when encountering a specific parse error.
     *
     * @return void
     */
    public function testParseError()
    {
        $this->expectPhpcsException('$stackPtr is not an OO constant');

        $const = $this->getTargetToken('/* testParseErrorLiveCoding */', \T_CONST);
        Constants::getProperties(self::$phpcsFile, $const);
    }
}
