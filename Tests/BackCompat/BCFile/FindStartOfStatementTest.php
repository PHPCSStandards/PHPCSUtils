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
 * Tests for the \PHPCSUtils\BackCompat\BCFile::findStartOfStatement method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::findStartOfStatement
 *
 * @since 1.0.0
 */
class FindStartOfStatementTest extends UtilityMethodTestCase
{

    /**
     * Test object call on result of static function call with arrow function as parameter and wrapped within an array.
     *
     * @link https://github.com/squizlabs/php_codesniffer/issues/2849
     * @link https://github.com/squizlabs/PHP_CodeSniffer/commit/fbf67efc3fc0c2a355f5585d49f4f6fe160ff2f9
     *
     * @return void
     */
    public function testObjectCallPrecededByArrowFunctionAsFunctionCallParameterInArray()
    {
        $expected = $this->getTargetToken('/* testPrecededByArrowFunctionInArray - Expected */', \T_STRING, 'Url');

        $start = $this->getTargetToken('/* testPrecededByArrowFunctionInArray */', \T_STRING, 'onlyOnDetail');
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame($expected, $found);
    }
}
