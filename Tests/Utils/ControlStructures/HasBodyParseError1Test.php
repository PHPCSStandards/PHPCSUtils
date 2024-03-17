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

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\ControlStructures;

/**
 * Tests for the \PHPCSUtils\Utils\ControlStructures::hasBody() method.
 *
 * @covers \PHPCSUtils\Utils\ControlStructures::hasBody
 *
 * @since 1.0.0
 */
final class HasBodyParseError1Test extends UtilityMethodTestCase
{

    /**
     * Test whether the function correctly identifies whether a control structure has a body
     * in the case of live coding.
     *
     * @return void
     */
    public function testHasBodyLiveCodingEmptyBody()
    {
        $stackPtr = $this->getTargetToken('/* testLiveCoding */', \T_ELSE);

        $result = ControlStructures::hasBody(self::$phpcsFile, $stackPtr);
        $this->assertTrue($result, 'Failed hasBody check with $allowEmpty = true');

        $result = ControlStructures::hasBody(self::$phpcsFile, $stackPtr, false);
        $this->assertFalse($result, 'Failed hasBody check with $allowEmpty = false');
    }
}
