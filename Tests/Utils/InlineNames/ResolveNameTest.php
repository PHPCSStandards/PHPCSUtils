<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\InlineNames;

use PHPCSUtils\Utils\InlineNames;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\InlineNames::resolveName() method.
 *
 * @covers \PHPCSUtils\Utils\InlineNames::resolveName
 * @covers \PHPCSUtils\Utils\InlineNames::arrayKeyToValueCaseInsensitive
 *
 * @group inlinenames
 *
 * @since 1.0.0
 */
class ResolveNameTest extends TestCase
{

    /**
     * Namespace to use as the $currentNamespace for the tests.
     *
     * @var string
     */
    private $currentNamespace = 'Test\Foo\Bar';

    /**
     * "Collected" use statements to use for the tests.
     *
     * @var array
     */
    private $useStatements = [
        'name'     => [
            'ClassABC'             => 'Vendor\Foo\ClassA',
            'InterfaceB'           => 'Vendor\Bar\InterfaceB',
            'ClassC'               => 'Vendor\Baz\ClassC',
            'ClassAlias'           => 'MyNamespace\YourClass',
            'ClassName'            => 'Some\NS\ClassName',
            'AnotherLevel'         => 'Some\NS\AnotherLevel',
            'Intërñâtîônælízàtiøn' => 'Iñtërnàtíønâlîzætiôn\Intërñâtîônælízàtiøn',
            '💩'                    => 'Emoji\💩',
        ],
        'function' => [
            'do_action'            => 'Monkey\See\do_action',
            'filter'               => 'Monkey\See\apply_filters',
            'intërñâtîônælízàtiøn' => 'Monkey\See\intërñâtîônælízàtiøn',
            '💩'                    => 'Emoji\💩',
        ],
        'const'    => [
            'PATH'                 => 'Monkey\Do\PATH',
            'RELEASE'              => 'Monkey\Do\VERSION',
            'INTËRÑÂTÎÔNÆLÍZÀTIØN' => 'Monkey\Do\INTËRÑÂTÎÔNÆLÍZÀTIØN',
            '💩'                    => 'Emoji\💩',
        ],
    ];

    /**
     * Empty variant of "Collected" use statements to use for the tests.
     *
     * @var array
     */
    private $minimalUse = [
        'name'     => [],
        'function' => [],
        'const'    => [],
    ];

    /**
     * Test receiving an expected exception when an invalid $name parameter is passed.
     *
     * @dataProvider dataInvalidName
     *
     * @param mixed $input The input value for $name to test with.
     *
     * @return void
     */
    public function testInvalidName($input)
    {
        $this->expectPhpcsException('Invalid input: $name must be a non-empty string');
        InlineNames::resolveName($input, 'name', $this->minimalUse, '');
    }

    /**
     * Data provider.
     *
     * @see testInvalidName() For the array format.
     *
     * @return array
     */
    public static function dataInvalidName()
    {
        return [
            'not-a-string'               => [10], // I.e. stack pointer passed.
            'empty-string'               => [''],
            'only-nullable'              => ['?'],
            'only-leading-backslash'     => ['\\'],
            'nullable-leading-backslash' => ['?\\'],
        ];
    }

    /**
     * Test receiving an expected exception when an invalid $type parameter is passed.
     *
     * @dataProvider dataInvalidType
     *
     * @param mixed $input The input value for $type to test with.
     *
     * @return void
     */
    public function testInvalidType($input)
    {
        $this->expectPhpcsException('Invalid input: $type must be either "name", "function" or "const"');
        InlineNames::resolveName('name', $input, $this->minimalUse, '');
    }

    /**
     * Data provider.
     *
     * @see testInvalidType() For the array format.
     *
     * @return array
     */
    public static function dataInvalidType()
    {
        return [
            'not-a-string'   => [null],
            'invalid-string' => ['class'],
        ];
    }

    /**
     * Test receiving an expected exception when an invalid $useStatements parameter is passed.
     *
     * @dataProvider dataInvalidUseStatements
     *
     * @param mixed $input The input value for $useStatements to test with.
     *
     * @return void
     */
    public function testInvalidUseStatements($input)
    {
        $this->expectPhpcsException(
            'Invalid input: $useStatements must be an array with the top-level keys "name", "function" and "const"'
        );
        InlineNames::resolveName('name', 'function', $input, '');
    }

    /**
     * Data provider.
     *
     * @see testInvalidUseStatements() For the array format.
     *
     * @return array
     */
    public static function dataInvalidUseStatements()
    {
        return [
            'not-an-array'     => [''],
            'empty-array'      => [[]],
            'incomplete-array' => [
                [
                    'name'     => [],
                    'function' => [],
                ],
            ],
        ];
    }

    /**
     * Test receiving an expected exception when an invalid $currentNamespace parameter is passed.
     *
     * @return void
     */
    public function testInvalidCurrentNamespace()
    {
        $this->expectPhpcsException('Invalid input: $currentNamespace must be a string (empty string allowed)');
        InlineNames::resolveName('name', 'const', $this->minimalUse, null);
    }

    /**
     * Test resolving an inline (class) name to its fully qualified form.
     *
     * @dataProvider dataResolveNameClass
     *
     * @param string $input    The input value to use for the $name parameter.
     * @param string $expected The expected function return value.
     *
     * @return void
     */
    public function testResolveNameClass($input, $expected)
    {
        $result = InlineNames::resolveName($input, 'name', $this->useStatements, $this->currentNamespace);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testResolveNameClass() For the array format.
     *
     * @return array
     */
    public static function dataResolveNameClass()
    {
        return [
            'self' => [
                'self',
                false,
            ],
            'self-uppercase' => [
                'SELF',
                false,
            ],
            'parent' => [
                'parent',
                false,
            ],
            'static' => [
                'static',
                false,
            ],
            'static-mixed-case' => [
                'StaTic',
                false,
            ],
            'unqualified-imported' => [
                'ClassC',
                '\Vendor\Baz\ClassC',
            ],
            'unqualified-imported-nullable' => [
                '?ClassC',
                '\Vendor\Baz\ClassC',
            ],
            'unqualified-imported-not-same-case' => [
                'interFaceb',
                '\Vendor\Bar\InterfaceB',
            ],
            'unqualified-imported-all-upper' => [
                'CLASSNAME',
                '\Some\NS\ClassName',
            ],
            'unqualified-imported-aliased' => [
                'ClassABC',
                '\Vendor\Foo\ClassA',
            ],
            'unqualified-not-imported' => [
                'NotImported',
                '\Test\Foo\Bar\NotImported',
            ],
            'partially-qualified-imported' => [
                'AnotherLevel\ClassName',
                '\Some\NS\AnotherLevel\ClassName',
            ],
            'partially-qualified-imported-not-same-case' => [
                'aNOTHERlEVEL\ClassName',
                '\Some\NS\AnotherLevel\ClassName',
            ],
            'partially-qualified-imported-nullable' => [
                '?AnotherLevel\ClassName',
                '\Some\NS\AnotherLevel\ClassName',
            ],
            'partially-qualified-not-imported' => [
                'SomeLevel\ClassName',
                '\Test\Foo\Bar\SomeLevel\ClassName',
            ],
            'fully-qualified' => [
                '\Fully\Qualified\Name',
                '\Fully\Qualified\Name',
            ],
            'fully-qualified-nullable' => [
                '?\Fully\Qualified\Name',
                '\Fully\Qualified\Name',
            ],
            'namespace-operator' => [
                'namespace\Sub\Name',
                '\Test\Foo\Bar\Sub\Name',
            ],
            'namespace-operator-extended-ascii' => [
                'namespace\Sub\Iñtërnâtîônàlízætiøn',
                '\Test\Foo\Bar\Sub\Iñtërnâtîônàlízætiøn',
            ],
            'unqualified-not-imported-extended-ascii' => [
                'Iñtërnâtîônàlízætiøn',
                '\Test\Foo\Bar\Iñtërnâtîônàlízætiøn',
            ],
            'unqualified-not-imported-extended-ascii-not-same-case' => [
                'IÑTËRNÂTÎÔNÀLÍZÆTIØN',
                '\Test\Foo\Bar\IÑTËRNÂTÎÔNÀLÍZÆTIØN',
            ],
            'partially-qualified-imported-extended-ascii' => [
                'Intërñâtîônælízàtiøn\Iñtërnàtíønâlîzætiôn',
                '\Iñtërnàtíønâlîzætiôn\Intërñâtîônælízàtiøn\Iñtërnàtíønâlîzætiôn',
            ],
            'partially-qualified-imported-extended-ascii-not-same-case' => [
                'INTëRñâTîôNæLíZàTIøN\Iñtërnàtíønâlîzætiôn',
                '\Iñtërnàtíønâlîzætiôn\Intërñâtîônælízàtiøn\Iñtërnàtíønâlîzætiôn',
            ],
            'unqualified-imported-emoji-name' => [
                '💩',
                '\Emoji\💩',
            ],
            'parse-error-namespace-operator-not-at-start' => [
                'Sub\namespace\Name',
                '\Test\Foo\Bar\Sub\namespace\Name',
            ],
        ];
    }

    /**
     * Test resolving an inline (class) name to its fully qualified form.
     *
     * @dataProvider dataResolveNameClassGlobalNamespace
     *
     * @param string $input    The input value to use for the $name parameter.
     * @param string $expected The expected function return value.
     *
     * @return void
     */
    public function testResolveNameClassGlobalNamespace($input, $expected)
    {
        $result = InlineNames::resolveName($input, 'name', $this->useStatements, '');
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testResolveNameClassGlobalNamespace() For the array format.
     *
     * @return array
     */
    public static function dataResolveNameClassGlobalNamespace()
    {
        return [
            'unqualified-imported' => [
                'ClassC',
                '\Vendor\Baz\ClassC',
            ],
            'unqualified-not-imported' => [
                'NotImported',
                '\NotImported',
            ],
            'partially-qualified-imported' => [
                'AnotherLevel\ClassName',
                '\Some\NS\AnotherLevel\ClassName',
            ],
            'partially-qualified-not-imported' => [
                'SomeLevel\ClassName',
                '\SomeLevel\ClassName',
            ],
            'namespace-operator' => [
                'namespace\Sub\Name',
                '\Sub\Name',
            ],
        ];
    }

   /**
     * Test resolving an inline (function) name to its fully qualified form.
     *
     * @dataProvider dataResolveNameFunction
     *
     * @param string $input    The input value to use for the $name parameter.
     * @param string $expected The expected function return value.
     *
     * @return void
     */
    public function testResolveNameFunction($input, $expected)
    {
        $result = InlineNames::resolveName($input, 'function', $this->useStatements, $this->currentNamespace);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testResolveNameFunction() For the array format.
     *
     * @return array
     */
    public static function dataResolveNameFunction()
    {
        return [
            'unqualified-imported' => [
                'do_action',
                '\Monkey\See\do_action',
            ],
            'unqualified-imported-not-same-case' => [
                'Do_Action',
                '\Monkey\See\do_action',
            ],
            'unqualified-imported-aliased' => [
                'filter',
                '\Monkey\See\apply_filters',
            ],
            'unqualified-not-imported' => [
                'NotImported',
                false,
            ],
            'partially-qualified-imported' => [
                'AnotherLevel\function_name',
                '\Some\NS\AnotherLevel\function_name',
            ],
            'partially-qualified-imported-not-same-case' => [
                'aNOTHERlEVEL\function_name',
                '\Some\NS\AnotherLevel\function_name',
            ],
            'partially-qualified-not-imported' => [
                'SomeLevel\function_name',
                false,
            ],
            'fully-qualified' => [
                '\Fully\Qualified\function_name',
                '\Fully\Qualified\function_name',
            ],
            'namespace-operator' => [
                'namespace\Sub\function_name',
                '\Test\Foo\Bar\Sub\function_name',
            ],
            'namespace-operator-extended-ascii' => [
                'namespace\Sub\Iñtërnâtîônàlízætiøn',
                '\Test\Foo\Bar\Sub\Iñtërnâtîônàlízætiøn',
            ],
            'unqualified-imported-extended-ascii' => [
                'intërñâtîônælízàtiøn',
                '\Monkey\See\intërñâtîônælízàtiøn',
            ],
            'unqualified-imported-extended-ascii-not-same-case' => [
                'INtëRñâtîôNæLíZàTiøN',
                '\Monkey\See\intërñâtîônælízàtiøn',
            ],
            'unqualified-not-imported-extended-ascii' => [
                'Iñtërnâtîônàlízætiøn',
                false,
            ],
            'unqualified-not-imported-extended-ascii-not-same-case' => [
                'IÑTËRNÂTÎÔNÀLÍZÆTIØN',
                false,
            ],
            'partially-qualified-imported-extended-ascii' => [
                'Intërñâtîônælízàtiøn\function_name',
                '\Iñtërnàtíønâlîzætiôn\Intërñâtîônælízàtiøn\function_name',
            ],
            'partially-qualified-imported-extended-ascii-not-same-case' => [
                'InTëRñâTîôNæLíZàTiøN\function_name',
                '\Iñtërnàtíønâlîzætiôn\Intërñâtîônælízàtiøn\function_name',
            ],
            'unqualified-imported-emoji-name' => [
                '💩',
                '\Emoji\💩',
            ],
        ];
    }

    /**
     * Test resolving an inline (function) name to its fully qualified form.
     *
     * @dataProvider dataResolveNameFunctionGlobalNamespace
     *
     * @param string $input    The input value to use for the $name parameter.
     * @param string $expected The expected function return value.
     *
     * @return void
     */
    public function testResolveNameFunctionGlobalNamespace($input, $expected)
    {
        $result = InlineNames::resolveName($input, 'function', $this->useStatements, '');
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testResolveNameFunctionGlobalNamespace() For the array format.
     *
     * @return array
     */
    public static function dataResolveNameFunctionGlobalNamespace()
    {
        return [
            'unqualified-imported' => [
                'do_action',
                '\Monkey\See\do_action',
            ],
            'unqualified-not-imported' => [
                'NotImported',
                '\NotImported',
            ],
            'partially-qualified-not-imported' => [
                'SomeLevel\function_name',
                '\SomeLevel\function_name',
            ],
            'namespace-operator' => [
                'namespace\Sub\function_name',
                '\Sub\function_name',
            ],
        ];
    }

   /**
     * Test resolving an inline (constant) name to its fully qualified form.
     *
     * @dataProvider dataResolveNameConstant
     *
     * @param string $input    The input value to use for the $name parameter.
     * @param string $expected The expected function return value.
     *
     * @return void
     */
    public function testResolveNameConstant($input, $expected)
    {
        $result = InlineNames::resolveName($input, 'const', $this->useStatements, $this->currentNamespace);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testResolveNameConstant() For the array format.
     *
     * @return array
     */
    public static function dataResolveNameConstant()
    {
        return [
            'unqualified-imported' => [
                'PATH',
                '\Monkey\Do\PATH',
            ],
            'unqualified-imported-not-same-case' => [
                'path',
                false,
            ],
            'unqualified-imported-aliased' => [
                'RELEASE',
                '\Monkey\Do\VERSION',
            ],
            'unqualified-not-imported' => [
                'NOTIMPORTED',
                false,
            ],
            'partially-qualified-imported' => [
                'AnotherLevel\CONSTANT_NAME',
                '\Some\NS\AnotherLevel\CONSTANT_NAME',
            ],
            'partially-qualified-imported-not-same-case' => [
                'aNOTHERlEVEL\CONSTANT_NAME',
                '\Some\NS\AnotherLevel\CONSTANT_NAME',
            ],
            'partially-qualified-not-imported' => [
                'SomeLevel\CONSTANT_NAME',
                false,
            ],
            'fully-qualified' => [
                '\Fully\Qualified\CONSTANT_NAME',
                '\Fully\Qualified\CONSTANT_NAME',
            ],
            'namespace-operator' => [
                'namespace\Sub\CONSTANT_NAME',
                '\Test\Foo\Bar\Sub\CONSTANT_NAME',
            ],
            'namespace-operator-extended-ascii' => [
                'namespace\Sub\INTËRÑÂTÎÔNÆLÍZÀTIØN',
                '\Test\Foo\Bar\Sub\INTËRÑÂTÎÔNÆLÍZÀTIØN',
            ],
            'unqualified-imported-extended-ascii' => [
                'INTËRÑÂTÎÔNÆLÍZÀTIØN',
                '\Monkey\Do\INTËRÑÂTÎÔNÆLÍZÀTIØN',
            ],
            'unqualified-imported-extended-ascii-not-same-case' => [
                'INtëRñâtîôNÆLÍZÀTiØn',
                false,
            ],
            'unqualified-not-imported-extended-ascii' => [
                'Iñtërnâtîônàlízætiøn',
                false,
            ],
            'unqualified-not-imported-extended-ascii-not-same-case' => [
                'IÑTËRNÂTÎÔNÀLÍZÆTIØN',
                false,
            ],
            'partially-qualified-imported-extended-ascii' => [
                'Intërñâtîônælízàtiøn\CONSTANT_NAME',
                '\Iñtërnàtíønâlîzætiôn\Intërñâtîônælízàtiøn\CONSTANT_NAME',
            ],
            'partially-qualified-imported-extended-ascii-not-same-case' => [
                'InTëRñâTîôNæLíZàTiøN\CONSTANT_NAME',
                '\Iñtërnàtíønâlîzætiôn\Intërñâtîônælízàtiøn\CONSTANT_NAME',
            ],
            'unqualified-imported-emoji-name' => [
                '💩',
                '\Emoji\💩',
            ],
        ];
    }

    /**
     * Test resolving an inline (constant) name to its fully qualified form.
     *
     * @dataProvider dataResolveNameConstantGlobalNamespace
     *
     * @param string $input    The input value to use for the $name parameter.
     * @param string $expected The expected function return value.
     *
     * @return void
     */
    public function testResolveNameConstantGlobalNamespace($input, $expected)
    {
        $result = InlineNames::resolveName($input, 'const', $this->useStatements, '');
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testResolveNameConstantGlobalNamespace() For the array format.
     *
     * @return array
     */
    public static function dataResolveNameConstantGlobalNamespace()
    {
        return [
            'unqualified-imported' => [
                'PATH',
                '\Monkey\Do\PATH',
            ],
            'unqualified-imported-not-same-case' => [
                'path',
                '\path',
            ],
            'unqualified-not-imported' => [
                'NOTIMPORTED',
                '\NOTIMPORTED',
            ],
            'partially-qualified-not-imported' => [
                'SomeLevel\CONSTANT_NAME',
                '\SomeLevel\CONSTANT_NAME',
            ],
            'partially-qualified-imported-not-same-case' => [
                'aNOTHERlEVEL\CONSTANT_NAME',
                '\Some\NS\AnotherLevel\CONSTANT_NAME',
            ],
            'namespace-operator' => [
                'namespace\Sub\CONSTANT_NAME',
                '\Sub\CONSTANT_NAME',
            ],
            'unqualified-imported-extended-ascii-not-same-case' => [
                'INtëRñâtîôNÆLÍZÀTiØn',
                '\INtëRñâtîôNÆLÍZÀTiØn',
            ],
        ];
    }

    /**
     * Helper method to tell PHPUnit to expect a PHPCS Exception in a PHPUnit cross-version
     * compatible manner.
     *
     * Duplicate of the same in the {@see \PHPCSUtils\TestUtils\UtilityMethodTestCase}.
     *
     * @param string $msg  The expected exception message.
     * @param string $type The exception type to expect. Either 'runtime' or 'tokenizer'.
     *                     Defaults to 'runtime'.
     *
     * @return void
     */
    public function expectPhpcsException($msg, $type = 'runtime')
    {
        $exception = 'PHP_CodeSniffer\Exceptions\RuntimeException';
        if ($type === 'tokenizer') {
            $exception = 'PHP_CodeSniffer\Exceptions\TokenizerException';
        }

        if (\method_exists($this, 'expectException')) {
            // PHPUnit 5+.
            $this->expectException($exception);
            $this->expectExceptionMessage($msg);
        } else {
            // PHPUnit 4.
            $this->setExpectedException($exception, $msg);
        }
    }
}
