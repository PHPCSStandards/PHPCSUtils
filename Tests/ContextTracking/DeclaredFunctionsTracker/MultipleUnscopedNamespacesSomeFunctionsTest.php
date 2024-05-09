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
final class MultipleUnscopedNamespacesSomeFunctionsTest extends GetFunctionsTestCase
{

    /**
     * List of all the function markers in the test case file and their FQN function name.
     *
     * @var array<string, string>
     */
    protected $functionMarkers = [
        '/* function: firstNSfnA */'                            => '\Unscoped\FirstNamespace\fnA',
        '/* function: firstNSfnB */'                            => '\Unscoped\FirstNamespace\fnB',
        '/* function: firstNSfnNestedInFunction */'             => '\Unscoped\FirstNamespace\fnNestedInFunction',
        '/* function: firstNSfnDoubleNestedInFunction */'       => '\Unscoped\FirstNamespace\fnDoubleNestedInFunction',
        '/* function: firstNSfnConditionallyDeclared */'        => '\Unscoped\FirstNamespace\fnConditionallyDeclared',
        '/* function: firstNSfnNestedInClassMethod */'          => '\Unscoped\FirstNamespace\fnNestedInClassMethod',
        '/* function: firstNSfnNestedInAnonClassMethod */'      => '\Unscoped\FirstNamespace\fnNestedInAnonClassMethod',
        '/* function: firstNSfnNestedInClosure */'              => '\Unscoped\FirstNamespace\fnNestedInClosure',
        '/* function: firstNSfnNestedInClosureInShortArray */'  => '\Unscoped\FirstNamespace\fnNestedInClosureInShortArray',
        '/* function: firstNSfnNestedInClosureInLongArray */'   => '\Unscoped\FirstNamespace\fnNestedInClosureInLongArray',

        '/* function: secondNSfnC */'                           => '\Unscoped\Second\Name\fnC',
        '/* function: secondNSfnD */'                           => '\Unscoped\Second\Name\fnD',
        '/* function: secondNSfnNestedInFunction */'            => '\Unscoped\Second\Name\fnNestedInFunction',
        '/* function: secondNSfnDoubleNestedInFunction */'      => '\Unscoped\Second\Name\fnDoubleNestedInFunction',
        '/* function: secondNSfnConditionallyDeclared */'       => '\Unscoped\Second\Name\fnConditionallyDeclared',
        '/* function: secondNSfnNestedInClassMethod */'         => '\Unscoped\Second\Name\fnNestedInClassMethod',
        '/* function: secondNSfnNestedInAnonClassMethod */'     => '\Unscoped\Second\Name\fnNestedInAnonClassMethod',
        '/* function: secondNSfnNestedInClosure */'             => '\Unscoped\Second\Name\fnNestedInClosure',
        '/* function: secondNSfnNestedInClosureInShortArray */' => '\Unscoped\Second\Name\fnNestedInClosureInShortArray',
        '/* function: secondNSfnNestedInClosureInLongArray */'  => '\Unscoped\Second\Name\fnNestedInClosureInLongArray',
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
            'namespace declared in file, function not declared' => [
                'input'    => '\Unscoped\FirstNamespace\doSomething',
                'expected' => false,
            ],
            'function declared in file, but provided with the wrong namespace name' => [
                'input'    => '\Unscoped\Second\Name\fnB',
                'expected' => false,
            ],
            'function declared in file, provided in same case' => [
                'input'    => '\Unscoped\FirstNamespace\fnNestedInClassMethod',
                'expected' => '/* function: firstNSfnNestedInClassMethod */',
            ],
            'function declared in file, function name different case, namespace name same case' => [
                'input'    => '\Unscoped\Second\Name\FNNESTEDINFUNCTION',
                'expected' => '/* function: secondNSfnNestedInFunction */',
            ],
        ];
    }
}
