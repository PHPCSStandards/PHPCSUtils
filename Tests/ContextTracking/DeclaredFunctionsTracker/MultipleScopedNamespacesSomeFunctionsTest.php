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
final class MultipleScopedNamespacesSomeFunctionsTest extends GetFunctionsTestCase
{

    /**
     * List of all the function markers in the test case file and their FQN function name.
     *
     * @var array<string, string>
     */
    protected $functionMarkers = [
        '/* function: firstNSfnA */'                           => '\Scoped\FirstNS\fnA',
        '/* function: firstNSfnB */'                           => '\Scoped\FirstNS\fnB',
        '/* function: firstNSfnNestedInFunction */'            => '\Scoped\FirstNS\fnNestedInFunction',
        '/* function: firstNSfnDoubleNestedInFunction */'      => '\Scoped\FirstNS\fnDoubleNestedInFunction',
        '/* function: firstNSfnConditionallyDeclared */'       => '\Scoped\FirstNS\fnConditionallyDeclared',
        '/* function: firstNSfnNestedInClassMethod */'         => '\Scoped\FirstNS\fnNestedInClassMethod',
        '/* function: firstNSfnNestedInAnonClassMethod */'     => '\Scoped\FirstNS\fnNestedInAnonClassMethod',
        '/* function: firstNSfnNestedInClosure */'             => '\Scoped\FirstNS\fnNestedInClosure',
        '/* function: firstNSfnNestedInClosureInShortArray */' => '\Scoped\FirstNS\fnNestedInClosureInShortArray',
        '/* function: firstNSfnNestedInClosureInLongArray */'  => '\Scoped\FirstNS\fnNestedInClosureInLongArray',

        '/* function: globalA */'                              => '\globalA',
        '/* function: globalB */'                              => '\globalB',
        '/* function: globalNestedInFunction */'               => '\globalNestedInFunction',
        '/* function: globalDoubleNestedInFunction */'         => '\globalDoubleNestedInFunction',
        '/* function: globalConditionallyDeclared */'          => '\globalConditionallyDeclared',
        '/* function: globalNestedInClassMethod */'            => '\globalNestedInClassMethod',
        '/* function: globalNestedInAnonClassMethod */'        => '\globalNestedInAnonClassMethod',
        '/* function: globalNestedInClosure */'                => '\globalNestedInClosure',
        '/* function: globalNestedInClosureInShortArray */'    => '\globalNestedInClosureInShortArray',
        '/* function: globalNestedInClosureInLongArray */'     => '\globalNestedInClosureInLongArray',

        '/* function: thirdNSfnA */'                           => '\Scoped\Third\Name\fnA',
        '/* function: thirdNSfnB */'                           => '\Scoped\Third\Name\fnB',
        '/* function: thirdNSfnNestedInFunction */'            => '\Scoped\Third\Name\fnNestedInFunction',
        '/* function: thirdNSfnDoubleNestedInFunction */'      => '\Scoped\Third\Name\fnDoubleNestedInFunction',
        '/* function: thirdNSfnConditionallyDeclared */'       => '\Scoped\Third\Name\fnConditionallyDeclared',
        '/* function: thirdNSfnNestedInClassMethod */'         => '\Scoped\Third\Name\fnNestedInClassMethod',
        '/* function: thirdNSfnNestedInAnonClassMethod */'     => '\Scoped\Third\Name\fnNestedInAnonClassMethod',
        '/* function: thirdNSfnNestedInClosure */'             => '\Scoped\Third\Name\fnNestedInClosure',
        '/* function: thirdNSfnNestedInClosureInShortArray */' => '\Scoped\Third\Name\fnNestedInClosureInShortArray',
        '/* function: thirdNSfnNestedInClosureInLongArray */'  => '\Scoped\Third\Name\fnNestedInClosureInLongArray',

        '/* function: globalAfterScopedParseError */'          => '\globalAfterScopedParseError',
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
                'input'    => '\global',
                'expected' => false,
            ],
            'namespace declared in file, function not declared' => [
                'input'    => '\Scoped\FirstNS\doSomething',
                'expected' => false,
            ],
            'function declared in file, but provided with the wrong namespace name' => [
                'input'    => '\Scoped\Third\Name\globalNestedInFunction',
                'expected' => false,
            ],
            'function declared in file, provided in same case, first namespace' => [
                'input'    => '\Scoped\FirstNS\fnNestedInClosure',
                'expected' => '/* function: firstNSfnNestedInClosure */',
            ],
            'function declared in file, provided in same case, second namespace' => [
                'input'    => '\globalDoubleNestedInFunction',
                'expected' => '/* function: globalDoubleNestedInFunction */',
            ],
            'function declared in file, provided in same case, third namespace' => [
                'input'    => '\Scoped\Third\Name\fnNestedInAnonClassMethod',
                'expected' => '/* function: thirdNSfnNestedInAnonClassMethod */',
            ],
            'function declared in file, provided in same case, after scoped' => [
                'input'    => '\globalAfterScopedParseError',
                'expected' => '/* function: globalAfterScopedParseError */',
            ],
        ];
    }
}
