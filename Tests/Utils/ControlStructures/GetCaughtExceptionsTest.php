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

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\ControlStructures;

/**
 * Tests for the \PHPCSUtils\Utils\ControlStructures::getCaughtExceptions() method.
 *
 * @covers \PHPCSUtils\Utils\ControlStructures::getCaughtExceptions
 *
 * @since 1.0.0
 */
final class GetCaughtExceptionsTest extends PolyfilledTestCase
{

    /**
     * Test receiving an expected exception when a non-integer token is passed.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        ControlStructures::getCaughtExceptions(self::$phpcsFile, false);
    }

    /**
     * Test receiving an expected exception when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object'
        );
        ControlStructures::getCaughtExceptions(self::$phpcsFile, 10000);
    }

    /**
     * Test receiving an expected exception when a non-CATCH token is passed.
     *
     * @return void
     */
    public function testNotCatch()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_CATCH; T_TRY given');

        $target = $this->getTargetToken('/* testNotCatch */', \T_TRY);
        ControlStructures::getCaughtExceptions(self::$phpcsFile, $target);
    }

    /**
     * Test retrieving the exceptions caught in a `catch` condition.
     *
     * @dataProvider dataGetCaughtExceptions
     *
     * @param string                           $testMarker The comment which prefaces the target token in the test file.
     * @param array<array<string, string|int>> $expected   The expected return value.
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
     * @return array<string, array<string, string|array<array<string, string|int>>>>
     */
    public static function dataGetCaughtExceptions()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'single-name-only' => [
                'testMarker'    => '/* testSingleCatchNameOnly */',
                'expected'      => [
                    [
                        'type'           => 'RuntimeException',
                        'type_token'     => 3,
                        'type_end_token' => 3,
                    ],
                ],
            ],
            'single-name-leading-backslash' => [
                'testMarker'    => '/* testSingleCatchNameLeadingBackslash */',
                'expected'      => [
                    [
                        'type'           => '\RuntimeException',
                        'type_token'     => 3,
                        'type_end_token' => ($php8Names === true) ? 3 : 4,
                    ],
                ],
            ],
            'single-partially-qualified' => [
                'testMarker'    => '/* testSingleCatchPartiallyQualified */',
                'expected'      => [
                    [
                        'type'           => 'MyNS\RuntimeException',
                        'type_token'     => 4,
                        'type_end_token' => ($php8Names === true) ? 4 : 6,
                    ],
                ],
            ],
            'single-fully-qualified' => [
                'testMarker'    => '/* testSingleCatchFullyQualified */',
                'expected'      => [
                    [
                        'type'           => '\MyNS\RuntimeException',
                        'type_token'     => 4,
                        'type_end_token' => ($php8Names === true) ? 4 : 7,
                    ],
                ],
            ],
            'single-name-with-comments-whitespace' => [
                'testMarker'    => '/* testSingleCatchPartiallyQualifiedWithCommentAndWhitespace */',
                'expected'      => [
                    [
                        'type'           => 'My\NS\Sub\RuntimeException',
                        'type_token'     => 4,
                        'type_end_token' => ($php8Names === true) ? 13 : 15,
                    ],
                ],
            ],
            'single-namespace-operator' => [
                'testMarker'    => '/* testSingleCatchNamespaceOperator */',
                'expected'      => [
                    [
                        'type'           => 'namespace\RuntimeException',
                        'type_token'     => 4,
                        'type_end_token' => ($php8Names === true) ? 4 : 6,
                    ],
                ],
            ],
            'multi-unqualified-names' => [
                'testMarker'    => '/* testMultiCatchSingleNames */',
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
                'testMarker'    => '/* testMultiCatchCompoundNames */',
                'expected'      => [
                    [
                        'type'           => '\NS\RuntimeException',
                        'type_token'     => 3,
                        'type_end_token' => ($php8Names === true) ? 3 : 6,
                    ],
                    [
                        'type'           => 'My\ParseErrorException',
                        'type_token'     => ($php8Names === true) ? 7 : 10,
                        'type_end_token' => ($php8Names === true) ? 7 : 12,
                    ],
                    [
                        'type'           => 'namespace\AnotherException',
                        'type_token'     => ($php8Names === true) ? 11 : 16,
                        'type_end_token' => ($php8Names === true) ? 15 : 20,
                    ],
                ],
            ],
            'non-capturing-catch' => [
                'testMarker'    => '/* testPHP8NonCapturingCatch */',
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
            'catch-without-named-exception' => [
                'testMarker'    => '/* testMissingExceptionName */',
                'expected'      => [],
            ],
            'multi-catch-without-named-exceptions' => [
                'testMarker'    => '/* testMultiMissingExceptionNames */',
                'expected'      => [],
            ],
            'live coding / parse error' => [
                'testMarker'    => '/* testLiveCoding */',
                'expected'      => [],
            ],
        ];
    }
}
