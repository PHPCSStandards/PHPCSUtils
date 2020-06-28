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
 * Tests for the \PHPCSUtils\Utils\ControlStructures::getCaughtExceptions() method.
 *
 * @covers \PHPCSUtils\Utils\ControlStructures::getCaughtExceptions
 *
 * @group controlstructures
 *
 * @since 1.0.0
 */
class GetCaughtExceptionsTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_CATCH');
        ControlStructures::getCaughtExceptions(self::$phpcsFile, 10000);
    }

    /**
     * Test receiving an expected exception when a non-CATCH token is passed.
     *
     * @return void
     */
    public function testNotCatch()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_CATCH');

        $target = $this->getTargetToken('/* testNotCatch */', \T_TRY);
        ControlStructures::getCaughtExceptions(self::$phpcsFile, $target);
    }

    /**
     * Test receiving an expected exception when a parse error is encountered.
     *
     * @return void
     */
    public function testParseError()
    {
        $this->expectPhpcsException('Parentheses opener/closer of the T_CATCH could not be determined');

        $target = $this->getTargetToken('/* testLiveCoding */', \T_CATCH);
        ControlStructures::getCaughtExceptions(self::$phpcsFile, $target);
    }

    /**
     * Test retrieving the exceptions caught in a `catch` condition.
     *
     * @dataProvider dataGetCaughtExceptions
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return value.
     *
     * @return void
     */
    public function testGetCaughtExceptions($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_CATCH);

        // Translate offsets to absolute token positions.
        foreach ($expected as $key => $value) {
            $expected[$key]['type_token']     += $stackPtr;
            $expected[$key]['type_end_token'] += $stackPtr;
        }

        $result = ControlStructures::getCaughtExceptions(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetCaughtExceptions() For the array format.
     *
     * @return array
     */
    public function dataGetCaughtExceptions()
    {
        return [
            'single-name-only' => [
                'target'        => '/* testSingleCatchNameOnly */',
                'expected'      => [
                    [
                        'type'           => 'RuntimeException',
                        'type_token'     => 3,
                        'type_end_token' => 3,
                    ],
                ],
            ],
            'single-name-leading-backslash' => [
                'target'        => '/* testSingleCatchNameLeadingBackslash */',
                'expected'      => [
                    [
                        'type'           => '\RuntimeException',
                        'type_token'     => 3,
                        'type_end_token' => 4,
                    ],
                ],
            ],
            'single-partially-qualified' => [
                'target'        => '/* testSingleCatchPartiallyQualified */',
                'expected'      => [
                    [
                        'type'           => 'MyNS\RuntimeException',
                        'type_token'     => 4,
                        'type_end_token' => 6,
                    ],
                ],
            ],
            'single-fully-qualified' => [
                'target'        => '/* testSingleCatchFullyQualified */',
                'expected'      => [
                    [
                        'type'           => '\MyNS\RuntimeException',
                        'type_token'     => 4,
                        'type_end_token' => 7,
                    ],
                ],
            ],
            'single-name-with-comments-whitespace' => [
                'target'        => '/* testSingleCatchPartiallyQualifiedWithCommentAndWhitespace */',
                'expected'      => [
                    [
                        'type'           => 'My\NS\Sub\RuntimeException',
                        'type_token'     => 4,
                        'type_end_token' => 15,
                    ],
                ],
            ],
            'single-namespace-operator' => [
                'target'        => '/* testSingleCatchNamespaceOperator */',
                'expected'      => [
                    [
                        'type'           => 'namespace\RuntimeException',
                        'type_token'     => 4,
                        'type_end_token' => 6,
                    ],
                ],
            ],
            'multi-unqualified-names' => [
                'target'        => '/* testMultiCatchSingleNames */',
                'expected'      => [
                    [
                        'type'           => 'RuntimeException',
                        'type_token'     => 3,
                        'type_end_token' => 3,
                    ],
                    [
                        'type'           => 'ParseErrorException',
                        'type_token'     => 7,
                        'type_end_token' => 7,
                    ],
                    [
                        'type'           => 'AnotherException',
                        'type_token'     => 11,
                        'type_end_token' => 11,
                    ],
                ],
            ],

            'multi-qualified-names' => [
                'target'        => '/* testMultiCatchCompoundNames */',
                'expected'      => [
                    [
                        'type'           => '\NS\RuntimeException',
                        'type_token'     => 3,
                        'type_end_token' => 6,
                    ],
                    [
                        'type'           => 'My\ParseErrorException',
                        'type_token'     => 10,
                        'type_end_token' => 12,
                    ],
                    [
                        'type'           => 'namespace\AnotherException',
                        'type_token'     => 16,
                        'type_end_token' => 20,
                    ],
                ],
            ],
            'non-capturing-catch' => [
                'target'        => '/* testPHP8NonCapturingCatch */',
                'expected'      => [
                    [
                        'type'           => 'RuntimeException',
                        'type_token'     => 3,
                        'type_end_token' => 3,
                    ],
                    [
                        'type'           => 'AnotherException',
                        'type_token'     => 7,
                        'type_end_token' => 7,
                    ],
                ],
            ],
        ];
    }
}
