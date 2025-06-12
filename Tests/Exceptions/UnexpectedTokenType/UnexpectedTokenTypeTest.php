<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Exceptions\UnexpectedTokenType;

use PHPCSUtils\Exceptions\UnexpectedTokenType;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Exceptions\UnexpectedTokenType
 *
 * @since 1.1.0
 */
final class UnexpectedTokenTypeTest extends TestCase
{

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreate()
    {
        $message = \sprintf(
            '%s(): Argument #2 ($stackPtr) must be of type T_NAMESPACE; T_WHITESPACE given',
            __METHOD__
        );

        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage($message);

        throw UnexpectedTokenType::create(2, '$stackPtr', 'T_NAMESPACE', 'T_WHITESPACE');
    }
}
