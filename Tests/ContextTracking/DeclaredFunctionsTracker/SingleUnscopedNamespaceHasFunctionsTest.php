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
final class SingleUnscopedNamespaceHasFunctionsTest extends GetFunctionsTestCase
{

    /**
     * List of all the function markers in the test case file and their FQN function name.
     *
     * @var array<string, string>
     */
    protected $functionMarkers = [
        '/* function: namespacedA */'                           => '\Unscoped\HasFunctions\namespacedA',
        '/* function: namespacedB */'                           => '\Unscoped\HasFunctions\namespacedB',
        '/* function: namespacedNestedInFunction */'            => '\Unscoped\HasFunctions\namespacedNestedInFunction',
        '/* function: namespacedDoubleNestedInFunction */'      => '\Unscoped\HasFunctions\namespacedDoubleNestedInFunction',
        '/* function: namespacedConditionallyDeclared */'       => '\Unscoped\HasFunctions\namespacedConditionallyDeclared',
        '/* function: namespacedNestedInClassMethod */'         => '\Unscoped\HasFunctions\namespacedNestedInClassMethod',
        '/* function: namespacedNestedInAnonClassMethod */'     => '\Unscoped\HasFunctions\namespacedNestedInAnonClassMethod',
        '/* function: namespacedNestedInClosure */'             => '\Unscoped\HasFunctions\namespacedNestedInClosure',
        '/* function: namespacedNestedInClosureInShortArray */' => '\Unscoped\HasFunctions\namespacedNestedInClosureInShortArray',
        '/* function: namespacedNestedInClosureInLongArray */'  => '\Unscoped\HasFunctions\namespacedNestedInClosureInLongArray',
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
                'input'    => '\doSomething',
                'expected' => false,
            ],
            'function declared in file, but provided name is missing the namespace' => [
                'input'    => '\namespacedDoubleNestedInFunction',
                'expected' => false,
            ],
            'function declared in file, but the namespace is different' => [
                'input'    => '\Vendor\Package\Name\namespacedNestedInClosureInShortArray',
                'expected' => false,
            ],
            'function declared in file, provided in same case' => [
                'input'    => '\Unscoped\HasFunctions\namespacedB',
                'expected' => '/* function: namespacedB */',
            ],
            'function declared in file, function name same case, namespace name different case' => [
                'input'    => '\unscoped\hasfunctions\namespacedNestedInClosureInLongArray',
                'expected' => '/* function: namespacedNestedInClosureInLongArray */',
            ],
        ];
    }
}
