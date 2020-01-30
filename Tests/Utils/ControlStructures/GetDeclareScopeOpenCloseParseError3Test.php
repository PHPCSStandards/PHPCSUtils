<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ControlStructures;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
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
class GetDeclareScopeOpenCloseParseError3Test extends UtilityMethodTestCase
{

    /**
     * Test that the function returns `false` in the case of a particular parse error.
     *
     * @return void
     */
    public function testGetDeclareScopeOpenCloseParseError()
    {
        $stackPtr = $this->getTargetToken('/* testNoScopeCloser */', \T_DECLARE);
        $result   = ControlStructures::getDeclareScopeOpenClose(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);
    }
}
