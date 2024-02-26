<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Tokens\Collections;

use PHPCSUtils\Tokens\Collections;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Tokens\Collections::namespacedNameTokens
 *
 * @since 1.0.0
 */
final class NamespacedNameTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testNamespacedNameTokens()
    {
        $expected = [
            \T_NS_SEPARATOR         => \T_NS_SEPARATOR,
            \T_NAMESPACE            => \T_NAMESPACE,
            \T_STRING               => \T_STRING,
            \T_NAME_QUALIFIED       => \T_NAME_QUALIFIED,
            \T_NAME_FULLY_QUALIFIED => \T_NAME_FULLY_QUALIFIED,
            \T_NAME_RELATIVE        => \T_NAME_RELATIVE,
        ];

        $this->assertSame($expected, Collections::namespacedNameTokens());
    }
}
