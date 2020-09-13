<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\FunctionDeclarations;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::getParameters method.
 *
 * The tests in this class cover the differences between the PHPCS native method and the PHPCSUtils
 * version. These tests would fail when using the BCFile `getParameters()` method.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getParameters
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
class GetParametersDiffTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_FUNCTION, T_CLOSURE or T_USE or an arrow function');

        FunctionDeclarations::getParameters(self::$phpcsFile, 10000);
    }

    /**
     * Verify recognition of PHP8 union type declaration.
     *
     * @return void
     */
    public function testPHP8UnionTypesSimple()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 8, // Offset from the T_FUNCTION token.
            'name'                => '$number',
            'content'             => 'int|float $number',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'int|float',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 6, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => 9,
        ];
        $expected[1] = [
            'token'               => 17, // Offset from the T_FUNCTION token.
            'name'                => '$obj',
            'content'             => 'self|parent &...$obj',
            'pass_by_reference'   => true,
            'reference_token'     => 15, // Offset from the T_FUNCTION token.
            'variable_length'     => true,
            'variadic_token'      => 16,
            'type_hint'           => 'self|parent',
            'type_hint_token'     => 11, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 13, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 union type declaration with a bitwise or in the default value.
     *
     * @return void
     */
    public function testPHP8UnionTypesSimpleWithBitwiseOrInDefault()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 6, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'int|float $var = CONSTANT_A | CONSTANT_B',
            'default'             => 'CONSTANT_A | CONSTANT_B',
            'default_token'       => 10, // Offset from the T_FUNCTION token.
            'default_equal_token' => 8, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'int|float',
            'type_hint_token'     => 2, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 4, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $arrowTokenTypes = Collections::arrowFunctionTokensBC();

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected, $arrowTokenTypes);
    }

    /**
     * Verify recognition of PHP8 union type declaration with two classes.
     *
     * @return void
     */
    public function testPHP8UnionTypesTwoClasses()
    {
        $php8Names = parent::usesPhp8NameTokens();

        $expected    = [];
        $expected[0] = [
            'token'               => ($php8Names === true) ? 8 : 11, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'MyClassA|\Package\MyClassB $var',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'MyClassA|\Package\MyClassB',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => ($php8Names === true) ? 6 : 9, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 union type declaration with all base types.
     *
     * @return void
     */
    public function testPHP8UnionTypesAllBaseTypes()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 20, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'array|bool|callable|int|float|null|object|string $var',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'array|bool|callable|int|float|null|object|string',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 18, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 union type declaration with all pseudo types..
     *
     * Note: "Resource" is not a type, but seen as a class name.
     *
     * @return void
     */
    public function testPHP8UnionTypesAllPseudoTypes()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 16, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'false|mixed|self|parent|iterable|Resource $var',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'false|mixed|self|parent|iterable|Resource',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 14, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 union type declaration with (illegal) nullability.
     *
     * @return void
     */
    public function testPHP8UnionTypesNullable()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 8, // Offset from the T_FUNCTION token.
            'name'                => '$number',
            'content'             => '?int|float $number',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int|float',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 6, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) single type null.
     *
     * @return void
     */
    public function testPHP8PseudoTypeNull()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 6, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'null $var = null',
            'default'             => 'null',
            'default_token'       => 10, // Offset from the T_FUNCTION token.
            'default_equal_token' => 8, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'null',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 4, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) single type false.
     *
     * @return void
     */
    public function testPHP8PseudoTypeFalse()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 6, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'false $var = false',
            'default'             => 'false',
            'default_token'       => 10, // Offset from the T_FUNCTION token.
            'default_equal_token' => 8, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'false',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 4, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) type false combined with type bool.
     *
     * @return void
     */
    public function testPHP8PseudoTypeFalseAndBool()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 8, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'bool|false $var = false',
            'default'             => 'false',
            'default_token'       => 12, // Offset from the T_FUNCTION token.
            'default_equal_token' => 10, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'bool|false',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 6, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) type object combined with a class name.
     *
     * @return void
     */
    public function testPHP8ObjectAndClass()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 8, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'object|ClassName $var',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'object|ClassName',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 6, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) type iterable combined with array/Traversable.
     *
     * @return void
     */
    public function testPHP8PseudoTypeIterableAndArray()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 10, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'iterable|array|Traversable $var',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'iterable|array|Traversable',
            'type_hint_token'     => 4, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 type declaration with (illegal) duplicate types.
     *
     * @return void
     */
    public function testPHP8DuplicateTypeInUnionWhitespaceAndComment()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 17, // Offset from the T_FUNCTION token.
            'name'                => '$var',
            'content'             => 'int | string /*comment*/ | INT $var',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'int|string|INT',
            'type_hint_token'     => 5, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 15, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 constructor property promotion without type declaration, with defaults.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionNoTypes()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 8, // Offset from the T_FUNCTION token.
            'name'                => '$x',
            'content'             => 'public $x = 0.0',
            'default'             => '0.0',
            'default_token'       => 12, // Offset from the T_FUNCTION token.
            'default_equal_token' => 10, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'visibility_token'    => 6, // Offset from the T_FUNCTION token.
            'comma_token'         => 13,
        ];
        $expected[1] = [
            'token'               => 18, // Offset from the T_FUNCTION token.
            'name'                => '$y',
            'content'             => 'protected $y = \'\'',
            'default'             => "''",
            'default_token'       => 22, // Offset from the T_FUNCTION token.
            'default_equal_token' => 20, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'protected',
            'visibility_token'    => 16, // Offset from the T_FUNCTION token.
            'comma_token'         => 23,
        ];
        $expected[2] = [
            'token'               => 28, // Offset from the T_FUNCTION token.
            'name'                => '$z',
            'content'             => 'private $z = null',
            'default'             => 'null',
            'default_token'       => 32, // Offset from the T_FUNCTION token.
            'default_equal_token' => 30, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 26, // Offset from the T_FUNCTION token.
            'comma_token'         => 33,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 constructor property promotion with type declarations.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionWithTypes()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 10, // Offset from the T_FUNCTION token.
            'name'                => '$x',
            'content'             => 'protected float|int $x',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'float|int',
            'type_hint_token'     => 6, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 8, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'property_visibility' => 'protected',
            'visibility_token'    => 4, // Offset from the T_FUNCTION token.
            'comma_token'         => 11,
        ];
        $expected[1] = [
            'token'               => 19, // Offset from the T_FUNCTION token.
            'name'                => '$y',
            'content'             => 'public ?string &$y = \'test\'',
            'default'             => "'test'",
            'default_token'       => 23, // Offset from the T_FUNCTION token.
            'default_equal_token' => 21, // Offset from the T_FUNCTION token.
            'pass_by_reference'   => true,
            'reference_token'     => 18, // Offset from the T_FUNCTION token.
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?string',
            'type_hint_token'     => 16, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 16, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'property_visibility' => 'public',
            'visibility_token'    => 13, // Offset from the T_FUNCTION token.
            'comma_token'         => 24,
        ];
        $expected[2] = [
            'token'               => 30, // Offset from the T_FUNCTION token.
            'name'                => '$z',
            'content'             => 'private mixed $z',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'mixed',
            'type_hint_token'     => 28, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 28, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 26, // Offset from the T_FUNCTION token.
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of PHP8 constructor with both property promotion as well as normal parameters.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionAndNormalParam()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 8, // Offset from the T_FUNCTION token.
            'name'                => '$promotedProp',
            'content'             => 'public int $promotedProp',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'int',
            'type_hint_token'     => 6, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 6, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'visibility_token'    => 4, // Offset from the T_FUNCTION token.
            'comma_token'         => 9,
        ];
        $expected[1] = [
            'token'               => 14, // Offset from the T_FUNCTION token.
            'name'                => '$normalArg',
            'content'             => '?int $normalArg',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?int',
            'type_hint_token'     => 12, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 12, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify behaviour when a non-constructor function uses PHP 8 property promotion syntax.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionGlobalFunction()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 6, // Offset from the T_FUNCTION token.
            'name'                => '$x',
            'content'             => 'private $x',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 4, // Offset from the T_FUNCTION token.
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify behaviour when an abstract constructor uses PHP 8 property promotion syntax.
     *
     * @return void
     */
    public function testPHP8ConstructorPropertyPromotionAbstractMethod()
    {
        $expected    = [];
        $expected[0] = [
            'token'               => 8, // Offset from the T_FUNCTION token.
            'name'                => '$y',
            'content'             => 'public callable $y',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => 'callable',
            'type_hint_token'     => 6, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => 6, // Offset from the T_FUNCTION token.
            'nullable_type'       => false,
            'property_visibility' => 'public',
            'visibility_token'    => 4, // Offset from the T_FUNCTION token.
            'comma_token'         => 9,
        ];
        $expected[1] = [
            'token'               => 14, // Offset from the T_FUNCTION token.
            'name'                => '$x',
            'content'             => 'private ...$x',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => true,
            'variadic_token'      => 13, // Offset from the T_FUNCTION token.
            'type_hint'           => '',
            'type_hint_token'     => false,
            'type_hint_end_token' => false,
            'nullable_type'       => false,
            'property_visibility' => 'private',
            'visibility_token'    => 11, // Offset from the T_FUNCTION token.
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Verify recognition of type declarations using the namespace operator.
     *
     * @return void
     */
    public function testNamespaceOperatorTypeHint()
    {
        $php8Names = parent::usesPhp8NameTokens();

        $expected    = [];
        $expected[0] = [
            'token'               => ($php8Names === true) ? 7 : 9, // Offset from the T_FUNCTION token.
            'name'                => '$var1',
            'content'             => '?namespace\Name $var1',
            'pass_by_reference'   => false,
            'reference_token'     => false,
            'variable_length'     => false,
            'variadic_token'      => false,
            'type_hint'           => '?namespace\Name',
            'type_hint_token'     => 5, // Offset from the T_FUNCTION token.
            'type_hint_end_token' => ($php8Names === true) ? 5 : 7, // Offset from the T_FUNCTION token.
            'nullable_type'       => true,
            'comma_token'         => false,
        ];

        $this->getParametersTestHelper('/* ' . __FUNCTION__ . ' */', $expected);
    }

    /**
     * Test helper.
     *
     * @param string $commentString The comment which preceeds the test.
     * @param array  $expected      The expected function output.
     * @param array  $targetType    Optional. The token type to search for after $commentString.
     *                              Defaults to the function/closure tokens.
     *
     * @return void
     */
    protected function getParametersTestHelper($commentString, $expected, $targetType = [\T_FUNCTION, \T_CLOSURE])
    {
        $target = $this->getTargetToken($commentString, $targetType);
        $found  = FunctionDeclarations::getParameters(self::$phpcsFile, $target);

        foreach ($expected as $key => $param) {
            $expected[$key]['token'] += $target;

            if ($param['reference_token'] !== false) {
                $expected[$key]['reference_token'] += $target;
            }
            if ($param['variadic_token'] !== false) {
                $expected[$key]['variadic_token'] += $target;
            }
            if ($param['type_hint_token'] !== false) {
                $expected[$key]['type_hint_token'] += $target;
            }
            if ($param['type_hint_end_token'] !== false) {
                $expected[$key]['type_hint_end_token'] += $target;
            }
            if ($param['comma_token'] !== false) {
                $expected[$key]['comma_token'] += $target;
            }
            if (isset($param['default_token'])) {
                $expected[$key]['default_token'] += $target;
            }
            if (isset($param['default_equal_token'])) {
                $expected[$key]['default_equal_token'] += $target;
            }
            if (isset($param['visibility_token'])) {
                $expected[$key]['visibility_token'] += $target;
            }
        }

        $this->assertSame($expected, $found);
    }
}
