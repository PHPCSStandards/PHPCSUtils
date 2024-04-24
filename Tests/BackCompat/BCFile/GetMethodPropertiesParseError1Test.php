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
use PHPCSUtils\Tokens\Collections;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getMethodProperties method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getMethodProperties
 *
 * @group functiondeclarations
 *
 * @since 1.0.11
 */
final class GetMethodPropertiesParseError1Test extends UtilityMethodTestCase
{

    /**
     * Test handling of closure declarations with an incomplete use clause.
     *
     * @return void
     */
    public function testParseError()
    {
        $target = $this->getTargetToken('/* testParseError */', Collections::functionDeclarationTokens());
        $result = BCFile::getMethodProperties(self::$phpcsFile, $target);

        $expected = [
            'scope'                 => 'public',
            'scope_specified'       => false,
            'return_type'           => '',
            'return_type_token'     => false,
            'return_type_end_token' => false,
            'nullable_return_type'  => false,
            'is_abstract'           => false,
            'is_final'              => false,
            'is_static'             => false,
            'has_body'              => false,
        ];

        $this->assertSame($expected, $result);
    }
}
