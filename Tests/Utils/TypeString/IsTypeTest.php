<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\TypeString;

use PHPCSUtils\Tests\TypeProviderHelper;
use PHPCSUtils\Utils\TypeString;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\TypeString::isSingular(), \PHPCSUtils\Utils\TypeString::isNullable(),
 * \PHPCSUtils\Utils\TypeString::isUnion(), \PHPCSUtils\Utils\TypeString::isIntersection()
 * and the \PHPCSUtils\Utils\TypeString::isDNF method.
 *
 * @covers \PHPCSUtils\Utils\TypeString::isSingular
 * @covers \PHPCSUtils\Utils\TypeString::isNullable
 * @covers \PHPCSUtils\Utils\TypeString::isUnion
 * @covers \PHPCSUtils\Utils\TypeString::isIntersection
 * @covers \PHPCSUtils\Utils\TypeString::isDNF
 *
 * @since 1.1.0
 */
final class IsTypeTest extends TestCase
{

    /**
     * Test isSingular() returns false when non-string data is passed.
     *
     * @dataProvider dataNonStringInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testIsSingularNonStringInput($input)
    {
        $this->assertFalse(TypeString::isSingular($input));
    }

    /**
     * Test isNullable() returns false when non-string data is passed.
     *
     * @dataProvider dataNonStringInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testIsNullableNonStringInput($input)
    {
        $this->assertFalse(TypeString::isNullable($input));
    }

    /**
     * Test isUnion() returns false when non-string data is passed.
     *
     * @dataProvider dataNonStringInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testIsUnionNonStringInput($input)
    {
        $this->assertFalse(TypeString::isUnion($input));
    }

    /**
     * Test isIntersection() returns false when non-string data is passed.
     *
     * @dataProvider dataNonStringInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testIsIntersectionNonStringInput($input)
    {
        $this->assertFalse(TypeString::isIntersection($input));
    }

    /**
     * Test isDNF() returns false when non-string data is passed.
     *
     * @dataProvider dataNonStringInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testIsDNFNonStringInput($input)
    {
        $this->assertFalse(TypeString::isDNF($input));
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataNonStringInput()
    {
        $data = TypeProviderHelper::getAll();
        unset(
            $data['empty string'],
            $data['numeric string'],
            $data['textual string'],
            $data['textual string starting with numbers']
        );

        return $data;
    }

    /**
     * Test isSingular().
     *
     * @dataProvider dataStringNotTypeString
     * @dataProvider dataTypeStrings
     *
     * @param string                $type     The type string.
     * @param array<string, string> $expected The expected function output.
     *
     * @return void
     */
    public function testIsSingular($type, $expected)
    {
        $this->assertSame($expected['singular'], TypeString::isSingular($type));
    }

    /**
     * Test isNullable().
     *
     * @dataProvider dataTypeStrings
     * @dataProvider dataStringNotTypeString
     *
     * @param string                $type     The type string.
     * @param array<string, string> $expected The expected function output.
     *
     * @return void
     */
    public function testIsNullable($type, $expected)
    {
        $this->assertSame($expected['nullable'], TypeString::isNullable($type));
    }

    /**
     * Test isUnion().
     *
     * @dataProvider dataTypeStrings
     * @dataProvider dataStringNotTypeString
     *
     * @param string                $type     The type string.
     * @param array<string, string> $expected The expected function output.
     *
     * @return void
     */
    public function testIsUnion($type, $expected)
    {
        $this->assertSame($expected['union'], TypeString::isUnion($type));
    }

    /**
     * Test isIntersection().
     *
     * @dataProvider dataTypeStrings
     * @dataProvider dataStringNotTypeString
     *
     * @param string                $type     The type string.
     * @param array<string, string> $expected The expected function output.
     *
     * @return void
     */
    public function testIsIntersection($type, $expected)
    {
        $this->assertSame($expected['intersection'], TypeString::isIntersection($type));
    }

    /**
     * Test isDNF().
     *
     * @dataProvider dataTypeStrings
     * @dataProvider dataStringNotTypeString
     *
     * @param string              $type     The type string.
     * @param array<string, bool> $expected The expected function output.
     *
     * @return void
     */
    public function testIsDNF($type, $expected)
    {
        $this->assertSame($expected['dnf'], TypeString::isDNF($type));
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, string|array<string, bool>>>
     */
    public static function dataTypeStrings()
    {
        return [
            'plain singular type: null' => [
                'type'     => 'null',
                'expected' => [
                    'singular'     => true,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'plain singular type: callable' => [
                'type'     => 'callable',
                'expected' => [
                    'singular'     => true,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'plain singular type: Countable' => [
                'type'     => 'Countable',
                'expected' => [
                    'singular'     => true,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'plain singular type: \ClassNameContainingNullInIt' => [
                'type'     => '\ClassNameContainingNullInIt',
                'expected' => [
                    'singular'     => true,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'plain singular type: \Class_With_Null_In_It' => [
                'type'     => '\Class_With_Null_In_It',
                'expected' => [
                    'singular'     => true,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],

            'nullable plain type: ?string' => [
                'type'     => '?string',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'nullable plain type: ?  ClassName (whitespace)' => [
                'type'     => '?  ClassName',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],

            'union type: all types' => [
                'type'     => 'array|bool|callable|false|FLOAT|Int|iterable|miXed|never|null|object|parent|'
                    . 'Self|static|string|true|void',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => true,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'union type: UnqualifiedName|Package\Partially|\Vendor\FullyQualified|namespace\Relative\Name' => [
                'type'     => 'UnqualifiedName|Package\Partially|\Vendor\FullyQualified|namespace\Relative\Name',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => true,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'nullable union type: NULL|INT (capitalized)' => [
                'type'     => 'NULL|INT',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => true,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'nullable union type: true | null (whitespace)' => [
                'type'     => 'true | null',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => true,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'nullable union type: \true|\null (FQN)' => [
                'type'     => '\true|\null',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => true,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],

            'intersection type: UnqualifiedName&Package\Partially&\Vendor\FullyQualified&namespace\Relative\Name' => [
                'type'     => 'UnqualifiedName&Package\Partially&\Vendor\FullyQualified&namespace\Relative\Name',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => true,
                    'dnf'          => false,
                ],
            ],
            'intersection type: bool&never (invalid)' => [
                'type'     => 'bool&never',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => true,
                    'dnf'          => false,
                ],
            ],
            'intersection type: Foo&null (invalid)' => [
                'type'     => 'Foo&null',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => true,
                    'dnf'          => false,
                ],
            ],

            'DNF type: string|(Foo&Bar)|int' => [
                'type'     => 'string|(Foo&Bar)|int',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => true,
                ],
            ],
            'DNF type: null at end' => [
                'type'     => '(Foo&Bar)|null',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => true,
                ],
            ],
            'DNF type: null in the middle' => [
                'type'     => '(Foo&Bar)|null|(Baz&Countable)',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => true,
                ],
            ],
            'DNF type: null at start and whitespace rich' => [
                'type'     => ' null | ( Foo & Bar & \Baz )',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => true,
                ],
            ],
            'DNF type: FQN null at end' => [
                'type'     => '(Foo&Bar)|\null',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => true,
                ],
            ],
            'DNF type: FQN null in the middle' => [
                'type'     => '(Foo&Bar)|\null|(Baz&Countable)',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => true,
                ],
            ],
            'DNF type: FQN null at start and whitespace rich' => [
                'type'     => ' \null | ( Foo & Bar & \Baz )',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => true,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => true,
                ],
            ],
        ];
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, string|array<string, bool>>>
     */
    public static function dataStringNotTypeString()
    {
        return [
            'empty string' => [
                'type'     => '',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'string containing only whitespace' => [
                'type'     => '       ',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],

            /*
             * Note: the methods don't check if a "type" is a valid identifier or if the format used is valid in PHP,
             * only that the type "looks like" a certain supported PHP type construct.
             * If a stricter check is needed, use the NamingConventions::isValidIdentifierName() method
             * for checking the individual types and token walking for checking the format.
             */
            'not a type string' => [
                'type'     => 'Roll roll roll your boat',
                'expected' => [
                    'singular'     => true,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string with commas' => [
                'type'     => 'Roll, roll, roll your boat',
                'expected' => [
                    'singular'     => true,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],

            'not a type string: only question mark (with whitespace)' => [
                'type'     => '  ?   ',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only question marks' => [
                'type'     => '? ?',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only question mark and pipe' => [
                'type'     => '?|',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only question mark and ampersand' => [
                'type'     => '? &',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only question mark and open parenthesis' => [
                'type'     => '?(',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only question mark and close parenthesis' => [
                'type'     => '? )',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: nullable ? not at start' => [
                'type'     => 'Some?\Class',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: nullable ? at end' => [
                'type'     => 'int?',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],

            'not a type string: only pipe' => [
                'type'     => '|',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only pipes' => [
                'type'     => '||',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only pipe and question mark' => [
                'type'     => '|?',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only pipe and ampersand' => [
                'type'     => '|&',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only pipe and open parenthesis' => [
                'type'     => '|(',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only pipe and close parenthesis' => [
                'type'     => '|)',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: pipe with something before, not after' => [
                'type'     => 'Something |',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: pipe with something after, not before' => [
                'type'     => '|Something',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: pipe with null before, nothing after' => [
                'type'     => 'null |',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: pipe with null after, nothing before' => [
                'type'     => '|null',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: multi-union pipe with null before, nothing after' => [
                'type'     => 'Something | null |',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: multi-union pipe with null after, nothing before' => [
                'type'     => '|null|Something',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],

            'not a type string: only ampersand' => [
                'type'     => '&',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only ampersands' => [
                'type'     => '&&',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only ampersand and question mark' => [
                'type'     => '&?',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only ampersand and pipe' => [
                'type'     => '&|',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only ampersand and open parenthesis' => [
                'type'     => '&(',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only ampersand and close parenthesis' => [
                'type'     => '&)',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: ampersand with something before, not after' => [
                'type'     => 'Something&',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: ampersand with something after, not before' => [
                'type'     => '&   Something',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: ampersand with null before, nothing after' => [
                'type'     => 'null&',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: ampersand with null after, nothing before' => [
                'type'     => '&   null',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: multi-intersect ampersand with null before, nothing after' => [
                'type'     => 'Something&null&',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: multi-intersect ampersand with null after, nothing before' => [
                'type'     => '&null&Something',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],

            'invalid type string: |null|' => [
                'type'     => '|null|',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'invalid type string: &null&' => [
                'type'     => '&null&',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],

            'not a type string: only open parenthesis' => [
                'type'     => '(',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only close parenthesis' => [
                'type'     => ')',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only open parentheses' => [
                'type'     => '((',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only close parentheses' => [
                'type'     => '))',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only parentheses ()' => [
                'type'     => '()',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only parentheses )(' => [
                'type'     => ')(',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only open parenthesis and question mark' => [
                'type'     => '(?',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only open parenthesis and pipe' => [
                'type'     => '(|',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only open parenthesis and ampersand' => [
                'type'     => '(&',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only close parenthesis and question mark' => [
                'type'     => ')&',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only close parenthesis and pipe' => [
                'type'     => ')|',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: only close parenthesis and ampersand' => [
                'type'     => ')&',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: open + close parentheses and ampersand' => [
                'type'     => '(&)',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: open + close parentheses and pipe' => [
                'type'     => '()|',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: open parenthesis, pipe and ampersand' => [
                'type'     => '(&|',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: close parenthesis, pipe and ampersand' => [
                'type'     => ')&|',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'not a type string: open + close parentheses, pipe and ampersand' => [
                'type'     => '(&)|',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],

            'Invalid DNF type: ?(Foo&Bar)' => [
                'type'     => '?(Foo&Bar)',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'Invalid DNF type: (Foo|Bar)&Baz' => [
                'type'     => '(Foo|Bar)&Baz',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'Invalid DNF type: Foo&Bar|string' => [
                'type'     => 'Foo&Bar|string',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'Invalid DNF type: (Foo&Bar|string)' => [
                'type'     => '(Foo&Bar|string)',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => true,
                ],
            ],
            'Incomplete DNF type: string|(Foo&Bar (missing close parentheses)' => [
                'type'     => 'string|(Foo&Bar',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
            'Incomplete DNF type: string|Foo&Bar) (missing open parentheses)' => [
                'type'     => 'string|Foo&Bar)',
                'expected' => [
                    'singular'     => false,
                    'nullable'     => false,
                    'union'        => false,
                    'intersection' => false,
                    'dnf'          => false,
                ],
            ],
        ];
    }
}
