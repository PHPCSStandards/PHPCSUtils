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
 * Test for the \PHPCSUtils\Utils\AttributeBlock::appliesTo() method.
 *
 * @covers \PHPCSUtils\Utils\AttributeBlock::appliesTo
 *
 * @group attributes
 *
 * @since 1.2.0
 */
final class AppliesToTest extends PolyfilledTestCase
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

        AttributeBlock::appliesTo(self::$phpcsFile, false);
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

        AttributeBlock::appliesTo(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a token which is not part of an attribute block is passed.
     *
     * @dataProvider dataNotAcceptedTypeException
     *
     * @param string     $identifier Comment which precedes the test case.
     * @param int|string $targetType Type of token to select as the target.
     *
     * @return void
     */
    public function testNotAcceptedTypeException($identifier, $targetType)
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be of type T_ATTRIBUTE, T_ATTRIBUTE_END or a token within an attribute'
        );

        $targetPtr = $this->getTargetToken($identifier, $targetType);
        AttributeBlock::appliesTo(self::$phpcsFile, $targetPtr);
    }

    /**
     * Data provider.
     *
     * @see testNotAcceptedTypeException()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataNotAcceptedTypeException()
    {
        return [
            'docblock, not attribute' => [
                'identifier' => '/* testNotAcceptedTypeExceptionOutsideAttribute1 */',
                'targetType' => \T_DOC_COMMENT_OPEN_TAG,
            ],
            'whitespace' => [
                'identifier' => '/* testNotAcceptedTypeExceptionOutsideAttribute1 */',
                'targetType' => \T_WHITESPACE,
            ],
            'function keyword, not attribute' => [
                'identifier' => '/* testNotAcceptedTypeExceptionOutsideAttribute1 */',
                'targetType' => \T_FUNCTION,
            ],
            'token which can\'t even exist in an attribute' => [
                'identifier' => '/* testNotAcceptedTypeExceptionOutsideAttribute2 */',
                'targetType' => \T_ECHO,
            ],
        ];
    }

    /**
     * Test that all tokens in an attribute block are accepted as the "stackPtr".
     *
     * @return void
     */
    public function testAllTokensFromAnAttributeBlockAreAccepted()
    {
        $tokens      = self::$phpcsFile->getTokens();
        $startPtr    = $this->getTargetToken('/* testAcceptedTokens */', \T_ATTRIBUTE);
        $endPtr      = $tokens[$startPtr]['attribute_closer'];
        $expectedPtr = $this->getTargetToken('/* testAcceptedTokens */', \T_FUNCTION);

        for ($i = $startPtr; $i <= $endPtr; $i++) {
            $this->assertSame(
                $expectedPtr,
                AttributeBlock::appliesTo(self::$phpcsFile, $i),
                "Token $i with type " . $tokens[$i]['type'] . ' did not yield correct results'
            );
        }
    }

    /**
     * Test the appliesTo() method.
     *
     * @dataProvider dataAppliesTo
     *
     * @param string     $identifier   Comment which precedes the test case.
     * @param int|string $expectedType Type of token which the attribute is expected to point to.
     *
     * @return void
     */
    public function testAppliesTo($identifier, $expectedType)
    {
        $targetPtr   = $this->getTargetToken($identifier, \T_ATTRIBUTE);
        $expectedPtr = $this->getTargetToken($identifier, $expectedType);
        $result      = AttributeBlock::appliesTo(self::$phpcsFile, $targetPtr);

        $this->assertSame($expectedPtr, $result);
    }

    /**
     * Data provider.
     *
     * @see testAppliesTo()
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataAppliesTo()
    {
        return [
            'unscoped constant' => [
                'identifier'   => '/* testFindUnscopedConst */',
                'expectedType' => \T_CONST,
            ],
            'unscoped closure' => [
                'identifier'   => '/* testFindUnscopedClosure */',
                'expectedType' => \T_CLOSURE,
            ],
            'unscoped closure, static' => [
                'identifier'   => '/* testFindUnscopedClosureStatic */',
                'expectedType' => \T_CLOSURE,
            ],
            'unscoped arrow function' => [
                'identifier'   => '/* testFindUnscopedArrowFunction */',
                'expectedType' => \T_FN,
            ],
            'unscoped arrow function, static' => [
                'identifier'   => '/* testFindUnscopedArrowFunctionStatic */',
                'expectedType' => \T_FN,
            ],
            'function param' => [
                'identifier'   => '/* testFindFunctionParameter */',
                'expectedType' => \T_VARIABLE,
            ],
            'function param with nullable type' => [
                'identifier'   => '/* testFindFunctionParameterTypedNullable */',
                'expectedType' => \T_VARIABLE,
            ],
            'function param with union type' => [
                'identifier'   => '/* testFindFunctionParameterTypedUnion */',
                'expectedType' => \T_VARIABLE,
            ],
            'function param pass by ref' => [
                'identifier'   => '/* testFindFunctionParameterWithRef */',
                'expectedType' => \T_VARIABLE,
            ],
            'function param variadic' => [
                'identifier'   => '/* testFindFunctionParameterWithSpread */',
                'expectedType' => \T_VARIABLE,
            ],
            'function param, DNF type, variadic, pass by ref' => [
                'identifier'   => '/* testFindFunctionParameterAllTogetherNow */',
                'expectedType' => \T_VARIABLE,
            ],
            'class' => [
                'identifier'   => '/* testFindClass */',
                'expectedType' => \T_CLASS,
            ],
            'class, final' => [
                'identifier'   => '/* testFindClassFinal */',
                'expectedType' => \T_CLASS,
            ],
            'class, readonly' => [
                'identifier'   => '/* testFindClassReadonly */',
                'expectedType' => \T_CLASS,
            ],
            'class, abstract' => [
                'identifier'   => '/* testFindClassAbstract */',
                'expectedType' => \T_CLASS,
            ],
            'class, final readonly' => [
                'identifier'   => '/* testFindClassFinalReadonly */',
                'expectedType' => \T_CLASS,
            ],
            'anon class' => [
                'identifier'   => '/* testFindAnonClass */',
                'expectedType' => \T_ANON_CLASS,
            ],
            'anon class, readonly' => [
                'identifier'   => '/* testFindAnonClassReadonly */',
                'expectedType' => \T_ANON_CLASS,
            ],
            'trait' => [
                'identifier'   => '/* testFindTrait */',
                'expectedType' => \T_TRAIT,
            ],
            'interface' => [
                'identifier'   => '/* testFindInterface */',
                'expectedType' => \T_INTERFACE,
            ],
            'enum' => [
                'identifier'   => '/* testFindEnum */',
                'expectedType' => \T_ENUM,
            ],
            'OO const, final' => [
                'identifier'   => '/* testFindConstFinal */',
                'expectedType' => \T_CONST,
            ],
            'OO const, public final' => [
                'identifier'   => '/* testFindConstPublicFinal */',
                'expectedType' => \T_CONST,
            ],
            'OO const, private typed' => [
                'identifier'   => '/* testFindConstPrivateTyped */',
                'expectedType' => \T_CONST,
            ],
            'OO property, var' => [
                'identifier'   => '/* testFindPropertyVar */',
                'expectedType' => \T_VARIABLE,
            ],
            'OO property, public static' => [
                'identifier'   => '/* testFindPropertyPublicStatic */',
                'expectedType' => \T_VARIABLE,
            ],
            'OO property, final protected readonly nullable type' => [
                'identifier'   => '/* testFindPropertyFinalProtectedReadonlyNullableType */',
                'expectedType' => \T_VARIABLE,
            ],
            'OO property, private abstract DNF type' => [
                'identifier'   => '/* testFindPropertyPrivateAbstractDNFType */',
                'expectedType' => \T_VARIABLE,
            ],
            'OO property, readonly private(set) intersection type' => [
                'identifier'   => '/* testFindPropertyReadonlyAsymPrivateIntersectionType */',
                'expectedType' => \T_VARIABLE,
            ],
            'OO method, public static, return by ref' => [
                'identifier'   => '/* testFindMethodPublicStaticReturnByRef */',
                'expectedType' => \T_FUNCTION,
            ],
            'OO method, abstract protected' => [
                'identifier'   => '/* testFindMethodAbstractProtected */',
                'expectedType' => \T_FUNCTION,
            ],
            'OO method, private final' => [
                'identifier'   => '/* testFindMethodPrivateFinal */',
                'expectedType' => \T_FUNCTION,
            ],
            'constructor property promotion' => [
                'identifier'   => '/* testFindPromotedProperty */',
                'expectedType' => \T_VARIABLE,
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
        $methodName   = 'PHPCSUtils\\Utils\\AttributeBlock::appliesTo';
        $cases        = self::dataAppliesTo();
        $identifier   = $cases['trait']['identifier'];
        $expectedType = $cases['trait']['expectedType'];

        $targetPtr   = $this->getTargetToken($identifier, \T_ATTRIBUTE);
        $expectedPtr = $this->getTargetToken($identifier, $expectedType);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = AttributeBlock::appliesTo(self::$phpcsFile, $targetPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $targetPtr);
        $resultSecondRun = AttributeBlock::appliesTo(self::$phpcsFile, $targetPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expectedPtr, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
