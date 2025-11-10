<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2025 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\AttributeHelper;

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\Variables;

/**
 * Tests for the \PHPCSUtils\Utils\Variables::getAttributeOpeners method.
 *
 * @covers \PHPCSUtils\Utils\Variables::getAttributeOpeners
 * @covers \PHPCSUtils\Internal\AttributeHelper::getOpeners
 *
 * @group attributes
 * @group variables
 *
 * @since 1.2.0
 */
final class GetOpenersForVariablesTest extends PolyfilledTestCase
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

        Variables::getAttributeOpeners(self::$phpcsFile, false);
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

        Variables::getAttributeOpeners(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a non variable token is passed.
     *
     * @return void
     */
    public function testNotAcceptedTypeException()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_VARIABLE;');

        $targetPtr = $this->getTargetToken('/* testNotAVariableToken */', \T_ECHO);
        Variables::getAttributeOpeners(self::$phpcsFile, $targetPtr);
    }

    /**
     * Test receiving an expected exception when a variable token is passed, which is not
     * an OO property, nor a function parameter.
     *
     * @dataProvider dataNotPropertyOrParamException
     *
     * @param string $identifier Comment which precedes the test case.
     *
     * @return void
     */
    public function testNotPropertyOrParamException($identifier)
    {
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage(
            'argument #2 ($stackPtr) must be the pointer to an OO property or a parameter in a function declaration.'
        );

        $targetPtr = $this->getTargetToken($identifier, \T_VARIABLE);
        Variables::getAttributeOpeners(self::$phpcsFile, $targetPtr);
    }

    /**
     * Data provider.
     *
     * @see testNotPropertyOrParamException()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataNotPropertyOrParamException()
    {
        return [
            'ordinary variable' => [
                'identifier' => '/* testVariableNotPropertyNorParam1 */',
            ],
            'static variable within function' => [
                'identifier' => '/* testVariableNotPropertyNorParam2 */',
            ],
        ];
    }

    /**
     * Test the getAttributeOpeners() method.
     *
     * @dataProvider dataGetAttributeOpenersParams
     * @dataProvider dataGetAttributeOpenersProps
     *
     * @param string     $identifier Comment which precedes the test case.
     * @param array<int> $expected   Expected function output.
     *
     * @return void
     */
    public function testGetAttributeOpeners($identifier, $expected)
    {
        $targetPtr = $this->getTargetToken($identifier, \T_VARIABLE);
        $expected  = $this->updateExpectedTokenPositions($targetPtr, $expected);
        $result    = Variables::getAttributeOpeners(self::$phpcsFile, $targetPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * Note: token positions are offsets in relation to the position of the T_VARIABLE token!
     *
     * @see testGetAttributeOpeners()
     *
     * @return array<string, array<string, string|array<int>>>
     */
    public static function dataGetAttributeOpenersParams()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'global function param, no type, with 1 attribute block' => [
                'identifier' => '/* testFunctionParamWithAttribute */',
                'expected'   => [-7],
            ],
            'global function param, no type, no attributes' => [
                'identifier' => '/* testFunctionParamNoAttribute */',
                'expected'   => [],
            ],
            'global function param, no type, ref, with 1 attribute block' => [
                'identifier' => '/* testFunctionParamRefWithAttribute */',
                'expected'   => [-15],
            ],
            'global function param, no type, ref, no attributes' => [
                'identifier' => '/* testFunctionParamRefNoAttribute */',
                'expected'   => [],
            ],
            'global function param, no type, spread, with 2 attribute blocks' => [
                'identifier' => '/* testFunctionParamSpreadWithAttribute */',
                'expected'   => [
                    -14,
                    -9,
                ],
            ],
            'global function param, no type, spread, no attributes' => [
                'identifier' => '/* testFunctionParamSpreadNoAttribute */',
                'expected'   => [],
            ],
            'global function param, no type, ref and spread, with 1 attribute block' => [
                'identifier' => '/* testFunctionParamRefAndSpreadWithAttribute */',
                'expected'   => [-19],
            ],

            'closure param, no type, with 1 attribute block' => [
                'identifier' => '/* testClosureParamWithAttributeSameLine */',
                'expected'   => [-6],
            ],
            'closure param, no type, no attributes' => [
                'identifier' => '/* testClosureParamNoAttribute */',
                'expected'   => [],
            ],

            'arrow function param, no type, with 1 attribute block' => [
                'identifier' => '/* testArrowParamWithAttributeSameLine */',
                'expected'   => [-6],
            ],
            'arrow function param, no type, no attributes' => [
                'identifier' => '/* testArrowParamNoAttribute */',
                'expected'   => [],
            ],

            'constructor prop promotion, public(set) static, union type, with 1 attribute block' => [
                'identifier' => '/* testCPPPropAsymPublicStaticUnionTypeWithAttribute */',
                'expected'   => [-15],
            ],
            'constructor prop promotion, final protected, nullable type, with 1 attribute block, same line' => [
                'identifier' => '/* testCPPPropFinalProtectedNullableTypeWithAttribute */',
                'expected'   => [-13],
            ],
            'constructor prop promotion, private(set), intersection type, no attributes' => [
                'identifier' => '/* testCPPPropAsymPrivateIntersectionTypeNoAttributes */',
                'expected'   => [],
            ],
            'constructor prop promotion, readonly , plain type, with 3 attribute blocks' => [
                'identifier' => '/* testCPPPropReadonlyPlainTypeWithAttribute */',
                'expected'   => [
                    -30,
                    -27,
                    -17,
                ],
            ],
            'constructor param, nullable type, ref and spread, with 1 attribute block' => [
                'identifier' => '/* testConstructorMethodParamNullableTypeRefAndSpreadWithAttributeSameLine */',
                'expected'   => [-12],
            ],

            'method param, plain type, with 1 attribute block' => [
                'identifier' => '/* testMethodParamTypedWithAttributeSameLine */',
                'expected'   => [-8],
            ],
            'method param, nullable type, ref and spread, no attributes' => [
                'identifier' => '/* testMethodParamNullableTypeNoAttribute */',
                'expected'   => [],
            ],
            'method param, union type, ref, with 1 attribute block' => [
                'identifier' => '/* testMethodParamUnionTypeRefWithAttribute */',
                'expected'   => [
                    ($php8Names === true ? -19 : -20),
                ],
            ],
            'method param, DNF type, ref and spread, with 1 attribute block' => [
                'identifier' => '/* testMethodParamDNFTypeRefAndSpreadWithAttribute */',
                'expected'   => [-27],
            ],

            'method param, no type, with 1 attribute block, same line' => [
                'identifier' => '/* testMethodParamNoTypeSameLine */',
                'expected'   => [-6],
            ],
        ];
    }

    /**
     * Data provider.
     *
     * Note: token positions are offsets in relation to the position of the T_VARIABLE token!
     *
     * @see testGetAttributeOpeners()
     *
     * @return array<string, array<string, string|array<int>>>
     */
    public static function dataGetAttributeOpenersProps()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'OO prop, var, no type, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyVarNoTypeWithAttribute */',
                'expected'   => [-9],
            ],
            'OO prop, public abstract, plain type, with 3 attribute blocks' => [
                'identifier' => '/* testOOPropertyPublicAbstractPlainTypeWithMultipleAttributes */',
                'expected'   => [
                    ($php8Names === true ? -51 : -54),
                    ($php8Names === true ? -38 : -41),
                    -15,
                ],
            ],
            'OO prop, final protected readonly, plain type, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyFinalProtectedReadonlyPlainTypeWithAttribute */',
                'expected'   => [-15],
            ],
            'OO prop, public static, no type, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyPrivateStaticNoTypeWithAttribute */',
                'expected'   => [-11],
            ],
            'OO prop, public(set), plain type, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyAsymPublicPlainTypeWithAttribute */',
                'expected'   => [-20],
            ],
            'OO prop, final protected(set), nullable type, with 3 attribute blocks' => [
                'identifier' => '/* testOOPropertyFinalAsymProtectedNullableTypeWithAttribute */',
                'expected'   => [
                    -29,
                    -26,
                    -20,
                ],
            ],
            'OO prop, public(set), nullable type, no attributes' => [
                'identifier' => '/* testOOPropertyAsymPublicNullableTypeNoAttributes */',
                'expected'   => [],
            ],
            'OO prop, private(set) public, OO type, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyAsymPrivatePublicOOTypeWithAttributeSameLine */',
                'expected'   => [-12],
            ],
            'OO prop, public final, namespace relative OO type, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyPublicFinalNamespaceRelativeTypeWithAttribute */',
                'expected'   => [
                    ($php8Names === true ? -22 : -24),
                ],
            ],
            'OO prop, protected private(set) final, partially qualified OO type, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyProtectedAsymPrivateFinalPartiallyQualifiedTypeWithAttribute */',
                'expected'   => [
                    ($php8Names === true ? -16 : -20),
                ],
            ],
            'OO prop, readonly, fully qualified OO type, with 3 attribute blocks' => [
                'identifier' => '/* testOOPropertyReadonlyFullyQualifiedTypeWithAttribute */',
                'expected'   => [
                    ($php8Names === true ? -27 : -34),
                    ($php8Names === true ? -22 : -29),
                    ($php8Names === true ? -11 : -16),
                ],
            ],
            'OO prop, public, all types, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyPublicAllValidTypesWithAttribute */',
                'expected'   => [-36],
            ],
            'OO prop, final, union type, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyFinalUnionOOTypesWithAttribute */',
                'expected'   => [
                    ($php8Names === true ? -22 : -25),
                ],
            ],
            'OO prop, static, intersection type, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyStaticIntersectionOOTypesWithAttribute */',
                'expected'   => [
                    ($php8Names === true ? -13 : -15),
                ],
            ],
            'OO prop, protected(set) static final, DNF type, with 1 attribute block' => [
                'identifier' => '/* testOOPropertyAsymProtectedStaticFinalDNFTypeWithAttribute */',
                'expected'   => [
                    ($php8Names === true ? -33 : -34),
                ],
            ],
            'OO multi-prop, public, plain type, with 1 attribute block' => [
                'identifier' => '/* testOOMultiPropertyLastWithAttribute */',
                'expected'   => [-28],
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
        $methodName = 'PHPCSUtils\\Internal\\AttributeHelper::getOpeners';
        $cases      = self::dataGetAttributeOpenersProps();
        $identifier = $cases['OO prop, public final, namespace relative OO type, with 1 attribute block']['identifier'];
        $expected   = $cases['OO prop, public final, namespace relative OO type, with 1 attribute block']['expected'];

        $targetPtr = $this->getTargetToken($identifier, \T_VARIABLE);
        $expected  = $this->updateExpectedTokenPositions($targetPtr, $expected);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = Variables::getAttributeOpeners(self::$phpcsFile, $targetPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, "{$targetPtr}-variable");
        $resultSecondRun = Variables::getAttributeOpeners(self::$phpcsFile, $targetPtr);

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
     * @param int        $targetPtr The token pointer to the target token from which
     *                              the offset is calculated.
     * @param array<int> $expected  The expected function output containing offsets.
     *
     * @return array<int>
     */
    private function updateExpectedTokenPositions($targetPtr, $expected)
    {
        foreach ($expected as $key => $value) {
            $expected[$key] += $targetPtr;
        }

        return $expected;
    }
}
