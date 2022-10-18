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
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getMethodParameters method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getMethodParameters
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
final class GetMethodParametersParseError2Test extends UtilityMethodTestCase
{

    /**
     * Test receiving an empty array when encountering a specific parse error.
     *
     * @return void
     */
    public function testParseError()
    {
        $target = $this->getTargetToken('/* testParseError */', Collections::functionDeclarationTokens());
        $result = BCFile::getMethodParameters(self::$phpcsFile, $target);

        $this->assertSame([], $result);
    }
}
