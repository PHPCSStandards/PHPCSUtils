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
     * Test the getProperties() method returns false for the name pointer, when the name is missing.
     *
     * @return void
     */
    public function testGetProperties()
    {
        $const    = $this->getTargetToken('/* testParseErrorMissingName */', \T_CONST);
        $expected = [
            'scope'           => 'private',
            'scope_token'     => ($const - 2),
            'is_final'        => false,
            'final_token'     => false,
            'type'            => '',
            'type_token'      => false,
            'type_end_token'  => false,
            'nullable_type'   => false,
            'name_token'      => false,
            'equal_token'     => ($const + 2),
        ];
        $result   = Constants::getProperties(self::$phpcsFile, $const);

        $this->assertSame($expected, $result);
    }
}
