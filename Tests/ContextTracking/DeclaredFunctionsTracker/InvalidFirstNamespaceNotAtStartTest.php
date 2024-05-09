<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\ContextTracking\DeclaredFunctionsTracker;

use PHPCSUtils\Tests\ContextTracking\DeclaredFunctionsTracker\GetFunctionsTestCase;

/**
 * Tests for the \PHPCSUtils\ContextTracking\DeclaredFunctionsTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\DeclaredFunctionsTracker
 *
 * @since 1.1.0
 */
final class InvalidFirstNamespaceNotAtStartTest extends GetFunctionsTestCase
{

    /**
     * List of all the function markers in the test case file and their FQN function name.
     *
     * @var array<string, string>
     */
    protected $functionMarkers = [
        '/* function: globalA */'                               => '\globalA',
        '/* function: globalB */'                               => '\globalB',
        '/* function: globalNestedInFunction */'                => '\globalNestedInFunction',
        '/* function: globalDoubleNestedInFunction */'          => '\globalDoubleNestedInFunction',
        '/* function: globalConditionallyDeclared */'           => '\globalConditionallyDeclared',

        '/* function: namespacedNestedInClassMethod */'         => '\Vendor\Package\Name\namespacedNestedInClassMethod',
        '/* function: namespacedNestedInAnonClassMethod */'     => '\Vendor\Package\Name\namespacedNestedInAnonClassMethod',
        '/* function: namespacedNestedInClosure */'             => '\Vendor\Package\Name\namespacedNestedInClosure',
        '/* function: namespacedNestedInClosureInShortArray */' => '\Vendor\Package\Name\namespacedNestedInClosureInShortArray',
        '/* function: namespacedNestedInClosureInLongArray */'  => '\Vendor\Package\Name\namespacedNestedInClosureInLongArray',
    ];

    /**
     * Data provider.
     *
     * @see testFindInFile() For the array format.
     *
     * @return array<string, array<string, string|false>>
     */
    public static function dataFindInFile()
    {
        return [
            'function not declared in file' => [
                'input'    => '\globalC',
                'expected' => false,
            ],
            'function declared in file, provided in same case' => [
                'input'    => '\Vendor\Package\Name\namespacedNestedInClosure',
                'expected' => '/* function: namespacedNestedInClosure */',
            ],
            'function declared in file, function name different case' => [
                'input'    => '\GlobalConditionallyDeclared',
                'expected' => '/* function: globalConditionallyDeclared */',
            ],
        ];
    }
}
