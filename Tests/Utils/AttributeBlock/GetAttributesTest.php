<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2025 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\AttributeBlock;

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\AttributeBlock;

/**
 * Tests for the \PHPCSUtils\Utils\AttributeBlock::getAttributes() and
 * \PHPCSUtils\Utils\AttributeBlock::countAttributes() methods.
 *
 * @covers \PHPCSUtils\Utils\AttributeBlock::getAttributes
 * @covers \PHPCSUtils\Utils\AttributeBlock::countAttributes
 *
 * @group attributes
 *
 * @since 1.2.0
 */
final class GetAttributesTest extends PolyfilledTestCase
{

    /**
     * Test receiving an exception when passing a non-integer token pointer.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        AttributeBlock::getAttributes(self::$phpcsFile, false);
    }

    /**
     * Test receiving an exception when passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given'
        );

        AttributeBlock::getAttributes(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a non attribute opener token is passed.
     *
     * @return void
     */
    public function testNotAcceptedTypeException()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_ATTRIBUTE;');

        $targetPtr = $this->getTargetToken('/* testNotAnAttributeOpener */', \T_ECHO);
        AttributeBlock::getAttributes(self::$phpcsFile, $targetPtr);
    }

    /**
     * Test the getAttributes() method.
     *
     * @dataProvider dataGetAttributes
     *
     * @param string                                      $identifier Comment which precedes the test case.
     * @param array<int, array<string, string|int|false>> $expected   Expected function output.
     *
     * @return void
     */
    public function testGetAttributes($identifier, $expected)
    {
        $targetPtr = $this->getTargetToken($identifier, \T_ATTRIBUTE);
        $expected  = $this->updateExpectedTokenPositions($targetPtr, $expected);
        $result    = AttributeBlock::getAttributes(self::$phpcsFile, $targetPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetAttributes()
     *
     * @return array<string, array<string, string|array<int, array<string, string|int|false>>>>
     */
    public static function dataGetAttributes()
    {
        $data = self::dataAttributes();
        foreach ($data as $key => $value) {
            unset($data[$key]['expectedCount']);
        }

        return $data;
    }

    /**
     * Test the countAttributes() method.
     *
     * @dataProvider dataCountAttributes
     *
     * @param string $identifier    Comment which precedes the test case.
     * @param int    $expectedCount Expected function output.
     *
     * @return void
     */
    public function testCountAttributes($identifier, $expectedCount)
    {
        $targetPtr = $this->getTargetToken($identifier, \T_ATTRIBUTE);
        $result    = AttributeBlock::countAttributes(self::$phpcsFile, $targetPtr);

        $this->assertSame($expectedCount, $result);
    }

    /**
     * Data provider.
     *
     * @see testCountAttributes()
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataCountAttributes()
    {
        $data = self::dataAttributes();
        foreach ($data as $key => $value) {
            unset($data[$key]['expected']);
        }

        return $data;
    }

    /**
     * Data provider.
     *
     * Note: token positions are offsets in relation to the position of the T_ATTRIBUTE token!
     *
     * @see testGetAttributes()
     *
     * @return array<string, array<string, string|array<int, array<string, string|int|false>>|int>>
     */
    public static function dataAttributes()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'empty attribute block' => [
                'identifier'    => '/* testEmptyAttributeBlock */',
                'expected'      => [],
                'expectedCount' => 0,
            ],

            'single attribute, no parentheses' => [
                'identifier'    => '/* testSingleAttributeNoParens */',
                'expected'      => [
                    0 => [
                        'name'        => 'SingleAttributeNoParens',
                        'name_token'  => 1,
                        'start'       => 1,
                        'end'         => 1,
                        'comma_token' => false,
                    ],
                ],
                'expectedCount' => 1,
            ],
            'single attribute, namespace relative name, no parentheses' => [
                'identifier'    => '/* testSingleAttributeNamespaceRelativeNoParens */',
                'expected'      => [
                    0 => [
                        'name'        => 'namespace\Relative',
                        'name_token'  => ($php8Names === true ? 1 : 3),
                        'start'       => 1,
                        'end'         => ($php8Names === true ? 1 : 3),
                        'comma_token' => false,
                    ],
                ],
                'expectedCount' => 1,
            ],
            'single attribute, partially qualified name, no parentheses' => [
                'identifier'    => '/* testSingleAttributePartiallyQualifiedNoParensTrailingComma */',
                'expected'      => [
                    0 => [
                        'name'        => 'Partially\Qualified\Name',
                        'name_token'  => ($php8Names === true ? 1 : 5),
                        'start'       => 1,
                        'end'         => ($php8Names === true ? 1 : 5),
                        'comma_token' => ($php8Names === true ? 2 : 6),
                    ],
                ],
                'expectedCount' => 1,
            ],
            'single attribute, fully qualified name, no parentheses' => [
                'identifier'    => '/* testSingleAttributeFullyQualifiedNoParensSpacey */',
                'expected'      => [
                    0 => [
                        'name'        => '\Fully\Qualified\Name',
                        'name_token'  => ($php8Names === true ? 2 : 7),
                        'start'       => 1,
                        'end'         => ($php8Names === true ? 3 : 8),
                        'comma_token' => false,
                    ],
                ],
                'expectedCount' => 1,
            ],
            'single attribute, parentheses, no parameters' => [
                'identifier'    => '/* testSingleAttributeParensNoParams */',
                'expected'      => [
                    0 => [
                        'name'        => 'SingleAttributeParensNoParams',
                        'name_token'  => 1,
                        'start'       => 1,
                        'end'         => 3,
                        'comma_token' => false,
                    ],
                ],
                'expectedCount' => 1,
            ],
            'single attribute, parentheses, no parameters, spacey with comments' => [
                'identifier'    => '/* testSingleAttributeParensNoParamsSpaceyWithComment */',
                'expected'      => [
                    0 => [
                        'name'        => 'SingleAttributeParensNoParamsSpacey',
                        'name_token'  => 2,
                        'start'       => 1,
                        'end'         => 11,
                        'comma_token' => false,
                    ],
                ],
                'expectedCount' => 1,
            ],
            'single attribute, with parameters' => [
                'identifier'    => '/* testSingleAttributeParensWithParamsTrailingComma */',
                'expected'      => [
                    0 => [
                        'name'        => 'SingleAttributeParensWithParams',
                        'name_token'  => 1,
                        'start'       => 1,
                        'end'         => 19,
                        'comma_token' => 20,
                    ],
                ],
                'expectedCount' => 1,
            ],
            'single attribute, with parameters, multi-line' => [
                'identifier'    => '/* testSingleAttributeParensWithParamsMultiLine */',
                'expected'      => [
                    0 => [
                        'name'        => 'SingleAttributeParensWithParamsMultiLine',
                        'name_token'  => 3,
                        'start'       => 1,
                        'end'         => 38,
                        'comma_token' => false,
                    ],
                ],
                'expectedCount' => 1,
            ],
            'single attribute, with named parameters, multi-line' => [
                'identifier'    => '/* testSingleAttributeWithNamedParams */',
                'expected'      => [
                    0 => [
                        'name'        => 'SingleAttributeWithNamedParams',
                        'name_token'  => 1,
                        'start'       => 1,
                        'end'         => 18,
                        'comma_token' => false,
                    ],
                ],
                'expectedCount' => 1,
            ],
            '4 attributes, single-line' => [
                'identifier'    => '/* testMultipleAttributesSingleLine */',
                'expected'      => [
                    0 => [
                        'name'        => 'FirstAttribute',
                        'name_token'  => 1,
                        'start'       => 1,
                        'end'         => 3,
                        'comma_token' => 4,
                    ],
                    1 => [
                        'name'        => 'namespace\SecondAttribute',
                        'name_token'  => ($php8Names === true ? 6 : 8),
                        'start'       => 5,
                        'end'         => ($php8Names === true ? 6 : 8),
                        'comma_token' => ($php8Names === true ? 7 : 9),
                    ],
                    2 => [
                        'name'        => '\ThirdAttribute',
                        'name_token'  => ($php8Names === true ? 9 : 12),
                        'start'       => ($php8Names === true ? 8 : 10),
                        'end'         => ($php8Names === true ? 15 : 18),
                        'comma_token' => ($php8Names === true ? 16 : 19),
                    ],
                    3 => [
                        'name'        => 'Partially\FourthAttribute',
                        'name_token'  => ($php8Names === true ? 18 : 23),
                        'start'       => ($php8Names === true ? 17 : 20),
                        'end'         => ($php8Names === true ? 21 : 26),
                        'comma_token' => false,
                    ],
                ],
                'expectedCount' => 4,
            ],
            '2 attributes, single-line, spacey' => [
                'identifier'    => '/* testMultipleAttributesSingleLineTrailingCommaSpacey */',
                'expected'      => [
                    0 => [
                        'name'        => 'FirstAttribute',
                        'name_token'  => 2,
                        'start'       => 1,
                        'end'         => 5,
                        'comma_token' => 6,
                    ],
                    1 => [
                        'name'        => '\Fully\Qualified\SecondAttribute',
                        'name_token'  => ($php8Names === true ? 8 : 13),
                        'start'       => 7,
                        'end'         => ($php8Names === true ? 9 : 14),
                        'comma_token' => ($php8Names === true ? 10 : 15),
                    ],
                ],
                'expectedCount' => 2,
            ],
            '4 attributes, multi-line, interlaced with comments' => [
                'identifier'    => '/* testMultipleAttributesMultiLineWithComments */',
                'expected'      => [
                    0 => [
                        'name'        => '\FirstAttribute',
                        'name_token'  => ($php8Names === true ? 3 : 4),
                        'start'       => 1,
                        'end'         => ($php8Names === true ? 5 : 6),
                        'comma_token' => ($php8Names === true ? 6 : 7),
                    ],
                    1 => [
                        'name'        => 'Partially\SecondAttribute',
                        'name_token'  => ($php8Names === true ? 11 : 14),
                        'start'       => ($php8Names === true ? 7 : 8),
                        'end'         => ($php8Names === true ? 11 : 14),
                        'comma_token' => ($php8Names === true ? 12 : 15),
                    ],
                    2 => [
                        'name'        => 'namespace\ThirdAttribute',
                        'name_token'  => ($php8Names === true ? 26 : 31),
                        'start'       => ($php8Names === true ? 13 : 16),
                        'end'         => ($php8Names === true ? 38 : 43),
                        'comma_token' => ($php8Names === true ? 39 : 44),
                    ],
                    3 => [
                        'name'        => 'FourthAttribute',
                        'name_token'  => ($php8Names === true ? 45 : 50),
                        'start'       => ($php8Names === true ? 40 : 45),
                        'end'         => ($php8Names === true ? 49 : 54),
                        'comma_token' => false,
                    ],
                ],
                'expectedCount' => 4,
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\AttributeBlock::getAttributes';
        $cases      = self::dataGetAttributes();
        $identifier = $cases['2 attributes, single-line, spacey']['identifier'];
        $expected   = $cases['2 attributes, single-line, spacey']['expected'];

        $targetPtr = $this->getTargetToken($identifier, \T_ATTRIBUTE);
        $expected  = $this->updateExpectedTokenPositions($targetPtr, $expected);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = AttributeBlock::getAttributes(self::$phpcsFile, $targetPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $targetPtr);
        $resultSecondRun = AttributeBlock::getAttributes(self::$phpcsFile, $targetPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }

    /**
     * Test helper to translate token offsets to absolute positions in an "expected" array.
     *
     * @param int                                         $targetPtr The token pointer to the target token from which
     *                                                               the offset is calculated.
     * @param array<int, array<string, string|int|false>> $expected  The expected function output containing offsets.
     *
     * @return array<int, array<string, string|int|false>>
     */
    private function updateExpectedTokenPositions($targetPtr, $expected)
    {
        foreach ($expected as $key => $attribute) {
            $expected[$key]['name_token'] += $targetPtr;
            $expected[$key]['start']      += $targetPtr;
            $expected[$key]['end']        += $targetPtr;

            if (\is_int($attribute['comma_token']) === true) {
                $expected[$key]['comma_token'] += $targetPtr;
            }
        }

        return $expected;
    }
}
