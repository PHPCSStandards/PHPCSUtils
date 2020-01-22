<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Utils;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Utils\GetTokensAsString;

/**
 * Utility functions for use when examining object declaration statements.
 *
 * @since 1.0.0 The `get(Declaration)Name()`, `getClassProperties()`, `findExtendedClassName()`
 *              and `findImplementedInterfaceNames()` methods are based on and
 *              inspired by the methods of the same name in the PHPCS native
 *              `File` class.
 *              Also see {@see \PHPCSUtils\BackCompat\BCFile}.
 */
class ObjectDeclarations
{

    /**
     * Retrieves the declaration name for classes, interfaces, traits, and functions.
     *
     * Note: For ES6 classes in combination with PHPCS 2.x, passing a `T_STRING` token to
     *       this method will be accepted for JS files.
     * Note: support for JS ES6 method syntax has not (yet) been back-filled for PHPCS < 3.0.0.
     *
     * Main differences with the PHPCS version:
     * - Defensive coding against incorrect calls to this method.
     * - Improved handling of invalid names, like names starting with a number.
     *   This allows sniffs to report on invalid names instead of ignoring them.
     * - Bug fix: improved handling of parse errors.
     *   Using the original method, a parse error due to an invalid name could cause the method
     *   to return the name of the *next* construct, a partial name and/or the name of a class
     *   being extended/interface being implemented.
     *   Using this version of the utility method, either the complete name (invalid or not) will
     *   be returned or `null` in case of no name (parse error).
     *
     * @see \PHP_CodeSniffer\Files\File::getDeclarationName()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::getDeclarationName() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the declaration token
     *                                               which declared the class, interface,
     *                                               trait, or function.
     *
     * @return string|null The name of the class, interface, trait, or function;
     *                     or NULL if the passed token doesn't exist, the function or
     *                     class is anonymous or in case of a parse error/live coding.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not of type
     *                                                      T_FUNCTION, T_CLASS, T_TRAIT, or T_INTERFACE.
     */
    public static function getName(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || ($tokens[$stackPtr]['code'] === \T_ANON_CLASS || $tokens[$stackPtr]['code'] === \T_CLOSURE)
        ) {
            return null;
        }

        $tokenCode = $tokens[$stackPtr]['code'];

        /*
         * BC: Work-around JS ES6 classes not being tokenized as T_CLASS in PHPCS < 3.0.0.
         */
        if ($phpcsFile->tokenizerType === 'JS'
            && $tokenCode === \T_STRING
            && $tokens[$stackPtr]['content'] === 'class'
        ) {
            $tokenCode = \T_CLASS;
        }

        if ($tokenCode !== \T_FUNCTION
            && $tokenCode !== \T_CLASS
            && $tokenCode !== \T_INTERFACE
            && $tokenCode !== \T_TRAIT
        ) {
            throw new RuntimeException(
                'Token type "' . $tokens[$stackPtr]['type'] . '" is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT'
            );
        }

        if ($tokenCode === \T_FUNCTION
            && \strtolower($tokens[$stackPtr]['content']) !== 'function'
        ) {
            // This is a function declared without the "function" keyword.
            // So this token is the function name.
            return $tokens[$stackPtr]['content'];
        }

        /*
         * Determine the name. Note that we cannot simply look for the first T_STRING
         * because an (invalid) class name starting with a number will be multiple tokens.
         * Whitespace or comment are however not allowed within a name.
         */

        $stopPoint = $phpcsFile->numTokens;
        if ($tokenCode === \T_FUNCTION && isset($tokens[$stackPtr]['parenthesis_opener']) === true) {
            $stopPoint = $tokens[$stackPtr]['parenthesis_opener'];
        } elseif (isset($tokens[$stackPtr]['scope_opener']) === true) {
            $stopPoint = $tokens[$stackPtr]['scope_opener'];
        }

        $exclude   = Tokens::$emptyTokens;
        $exclude[] = \T_OPEN_PARENTHESIS;
        $exclude[] = \T_OPEN_CURLY_BRACKET;

        $nameStart = $phpcsFile->findNext($exclude, ($stackPtr + 1), $stopPoint, true);
        if ($nameStart === false) {
            // Live coding or parse error.
            return null;
        }

        $tokenAfterNameEnd = $phpcsFile->findNext($exclude, $nameStart, $stopPoint);

        if ($tokenAfterNameEnd === false) {
            $content = null;

            /*
             * BC: In PHPCS 2.6.0, in case of live coding, the last token in a file will be tokenized
             * as T_STRING, but won't have the `content` index set.
             */
            if (isset($tokens[$nameStart]['content'])) {
                $content = $tokens[$nameStart]['content'];
            }

            return $content;
        }

        // Name starts with number, so is composed of multiple tokens.
        return GetTokensAsString::noEmpties($phpcsFile, $nameStart, ($tokenAfterNameEnd - 1));
    }

    /**
     * Retrieves the implementation properties of a class.
     *
     * The format of the return value is:
     * <code>
     *   array(
     *    'is_abstract' => false, // true if the abstract keyword was found.
     *    'is_final'    => false, // true if the final keyword was found.
     *   );
     * </code>
     *
     * @see \PHP_CodeSniffer\Files\File::getClassProperties()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::getClassProperties() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the T_CLASS
     *                                               token to acquire the properties for.
     *
     * @return array
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_CLASS token.
     */
    public static function getClassProperties(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== \T_CLASS) {
            throw new RuntimeException('$stackPtr must be of type T_CLASS');
        }

        $valid = [
            \T_FINAL       => \T_FINAL,
            \T_ABSTRACT    => \T_ABSTRACT,
            \T_WHITESPACE  => \T_WHITESPACE,
            \T_COMMENT     => \T_COMMENT,
            \T_DOC_COMMENT => \T_DOC_COMMENT,
        ];

        $isAbstract = false;
        $isFinal    = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$tokens[$i]['code']]) === false) {
                break;
            }

            switch ($tokens[$i]['code']) {
                case \T_ABSTRACT:
                    $isAbstract = true;
                    break;

                case \T_FINAL:
                    $isFinal = true;
                    break;
            }
        }

        return [
            'is_abstract' => $isAbstract,
            'is_final'    => $isFinal,
        ];
    }

    /**
     * Retrieves the name of the class that the specified class extends.
     * (works for classes, anonymous classes and interfaces)
     *
     * @see \PHP_CodeSniffer\Files\File::findExtendedClassName()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::findExtendedClassName() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The stack position of the class.
     *
     * @return string|false The extended class name or FALSE on error or if there
     *                      is no extended class name.
     */
    public static function findExtendedClassName(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] !== \T_CLASS
            && $tokens[$stackPtr]['code'] !== \T_ANON_CLASS
            && $tokens[$stackPtr]['code'] !== \T_INTERFACE
        ) {
            return false;
        }

        if (isset($tokens[$stackPtr]['scope_opener']) === false) {
            return false;
        }

        $classOpenerIndex = $tokens[$stackPtr]['scope_opener'];
        $extendsIndex     = $phpcsFile->findNext(\T_EXTENDS, $stackPtr, $classOpenerIndex);
        if ($extendsIndex === false) {
            return false;
        }

        $find = [
            \T_NS_SEPARATOR,
            \T_STRING,
            \T_WHITESPACE,
        ];

        $end  = $phpcsFile->findNext($find, ($extendsIndex + 1), ($classOpenerIndex + 1), true);
        $name = $phpcsFile->getTokensAsString(($extendsIndex + 1), ($end - $extendsIndex - 1));
        $name = \trim($name);

        if ($name === '') {
            return false;
        }

        return $name;
    }

    /**
     * Retrieves the names of the interfaces that the specified class implements.
     *
     * @see \PHP_CodeSniffer\Files\File::findImplementedInterfaceNames()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::findImplementedInterfaceNames() Cross-version compatible version of
     *                                                                     the original.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The stack position of the class.
     *
     * @return array|false Array with names of the implemented interfaces or FALSE on
     *                     error or if there are no implemented interface names.
     */
    public static function findImplementedInterfaceNames(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Check for the existence of the token.
        if (isset($tokens[$stackPtr]) === false) {
            return false;
        }

        if ($tokens[$stackPtr]['code'] !== \T_CLASS
            && $tokens[$stackPtr]['code'] !== \T_ANON_CLASS
        ) {
            return false;
        }

        if (isset($tokens[$stackPtr]['scope_closer']) === false) {
            return false;
        }

        $classOpenerIndex = $tokens[$stackPtr]['scope_opener'];
        $implementsIndex  = $phpcsFile->findNext(\T_IMPLEMENTS, $stackPtr, $classOpenerIndex);
        if ($implementsIndex === false) {
            return false;
        }

        $find = [
            \T_NS_SEPARATOR,
            \T_STRING,
            \T_WHITESPACE,
            \T_COMMA,
        ];

        $end  = $phpcsFile->findNext($find, ($implementsIndex + 1), ($classOpenerIndex + 1), true);
        $name = $phpcsFile->getTokensAsString(($implementsIndex + 1), ($end - $implementsIndex - 1));
        $name = \trim($name);

        if ($name === '') {
            return false;
        } else {
            $names = \explode(',', $name);
            $names = \array_map('trim', $names);
            return $names;
        }
    }
}
