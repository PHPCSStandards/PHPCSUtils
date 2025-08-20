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
 * Tests for the \PHPCSUtils\Utils\TypeString::normalizeCase() method.
 *
 * @covers \PHPCSUtils\Utils\TypeString::normalizeCase
 *
 * @since 1.1.0
 */
final class NormalizeCaseTest extends TestCase
{

    /**
     * Test case normalization returns an empty string when non-string data is passed.
     *
     * @dataProvider dataNormalizeCaseReturnsEmptyStringOnNonStringInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testNormalizeCaseReturnsEmptyStringOnNonStringInput($input)
    {
        $this->assertSame('', TypeString::normalizeCase($input));
    }

    /**
     * Data provider.
     *
     * @see testNormalizeCaseReturnsEmptyStringOnNonStringInput() For the array format.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataNormalizeCaseReturnsEmptyStringOnNonStringInput()
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
     * Test case normalization.
     *
     * Includes tests safeguarding that case normalization does not change the whitespace in the string.
     *
     * @dataProvider dataNormalizeCase
     *
     * @param string $type     The type.
     * @param string $expected The expected function output.
     *
     * @return void
     */
    public function testNormalizeCase($type, $expected)
    {
        $this->assertSame($expected, TypeString::normalizeCase($type));
    }

    /**
     * Data provider.
     *
     * @see testNormalizeCase() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNormalizeCase()
    {
        $data                 = [];
        $data['empty string'] = [
            'type'     => '',
            'expected' => '',
        ];
        $data['string containing only whitespace'] = [
            'type'     => '     ',
            'expected' => '     ',
        ];
        $data['string which isn\'t a type string'] = [
            'type'     => 'Roll, roll, roll your boat',
            'expected' => 'Roll, roll, roll your boat',
        ];

        $types = [
            'array'    => 'array',
            'bool'     => 'bool',
            'callable' => 'callable',
            'false'    => 'false',
            'float'    => 'float',
            'int'      => 'int',
            'iterable' => 'iterable',
            'mixed'    => 'mixed',
            'never'    => 'never',
            'null'     => 'null',
            'object'   => 'object',
            'parent'   => 'parent',
            'self'     => 'self',
            'static'   => 'static',
            'string'   => 'string',
            'true'     => 'true',
            'void'     => 'void',
        ];

        foreach ($types as $type => $expected) {
            $data['Keyword ' . $type . ': lowercase'] = [
                'type'     => $type,
                'expected' => $expected,
            ];

            $data['Keyword ' . $type . ': uppercase'] = [
                'type'     => \strtoupper($type),
                'expected' => $expected,
            ];

            $data['Keyword ' . $type . ': mixed case'] = [
                'type'     => \ucfirst($type),
                'expected' => $expected,
            ];
        }

        $data['true: fully qualified']             = [
            'type'     => '\true',
            'expected' => 'true',
        ];
        $data['false: fully qualified']            = [
            'type'     => '\false',
            'expected' => 'false',
        ];
        $data['null: fully qualified']             = [
            'type'     => '\null',
            'expected' => 'null',
        ];
        $data['true: fully qualified, uppercase']  = [
            'type'     => '\TRUE',
            'expected' => 'true',
        ];
        $data['false: fully qualified, uppercase'] = [
            'type'     => '\FALSE',
            'expected' => 'false',
        ];
        $data['null: fully qualified, uppercase']  = [
            'type'     => '\NULL',
            'expected' => 'null',
        ];

        $data['Classname: UnqualifiedName'] = [
            'type'     => 'UnqualifiedName',
            'expected' => 'UnqualifiedName',
        ];

        $data['Classname: Package\Partially'] = [
            'type'     => 'Package\Partially',
            'expected' => 'Package\Partially',
        ];

        $data['Classname: \Vendor\Package\FullyQualified'] = [
            'type'     => '\Vendor\Package\FullyQualified',
            'expected' => '\Vendor\Package\FullyQualified',
        ];

        $data['Classname: namespace\Relative\Name'] = [
            'type'     => 'namespace\Relative\Name',
            'expected' => 'namespace\Relative\Name',
        ];

        $data['Classname: Пасха (non-ascii chars)'] = [
            'type'     => 'Пасха',
            'expected' => 'Пасха',
        ];

        $data['Classname: 😎 (non-ascii chars/emoji name)'] = [
            'type'     => '😎',
            'expected' => '😎',
        ];

        // Document whitespace handling: whitespace will not be changed by this method.
        $data['Keyword iterable: lowercase - surrounding whitespace is not changed'] = [
            'type'     => '  iterable  ',
            'expected' => '  iterable  ',
        ];

        $data['Keyword static: uppercase - surrounding whitespace is not changed'] = [
            'type'     => '  STATIC  ',
            'expected' => '  static  ',
        ];

        $data['Keyword bool: mixed case - surrounding whitespace is not changed'] = [
            'type'     => "     Bool  \t\n",
            'expected' => "     bool  \t\n",
        ];

        $data['Classname: Traversable - surrounding whitespace is not changed'] = [
            'type'     => '  Traversable   ' . "\n\t\n",
            'expected' => '  Traversable   ' . "\n\t\n",
        ];

        $data['Classname: \Vendor\Package\FullyQualified - whitespace within name is not changed'] = [
            'type'     => '\Vendor  \  Package  \  FullyQualified',
            'expected' => '\Vendor  \  Package  \  FullyQualified',
        ];

        return $data;
    }
}
