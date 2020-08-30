<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Utils;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Conditions;
use PHPCSUtils\Utils\Namespaces;
use PHPCSUtils\Utils\NamingConventions;
use PHPCSUtils\Utils\ObjectDeclarations;
use PHPCSUtils\Utils\TextStrings;

/**
 * Utility functions for retrieving and resolving construct names used in inline code.
 *
 * @since 1.0.0
 */
class InlineNames
{

    /**
     * Resolve a construct name as used inline to it's fully qualified name, in as far as possible.
     *
     * This includes translating alias names to the real class name.
     *
     * This method should be passed a single construct name retrieved via any of the below listed methods:
     * - {@see \PHPCSUtils\Utils\ControlStructures::getCaughtExceptions()} (`type` index)
     * - {@see \PHPCSUtils\Utils\FunctionDeclarations::getProperties()} (`return_type` index without
     *       nullability operator and split on union type operator)
     * - {@see \PHPCSUtils\Utils\FunctionDeclarations::getParameters()} (`type_hint` index without
     *       nullability operator and split on union type operator)
     * - {@see \PHPCSUtils\Utils\InlineNames::getNameFromNew()}
     * - {@see \PHPCSUtils\Utils\InlineNames::getNameFromDoubleColon()}
     * - {@see \PHPCSUtils\Utils\ObjectDeclarations::findExtendedClassName()}
     * - {@see \PHPCSUtils\Utils\ObjectDeclarations::findImplementedInterfaceNames()}
     * - {@see \PHPCSUtils\Utils\ObjectDeclarations::findExtendedInterfaceNames()}
     * - {@see \PHPCSUtils\Utils\Variables::getMemberProperties()} (`type` index without nullability
     *       operator and split on union type operator)
     *
     * @since 1.0.0
     *
     * @param string $name             The construct name as found inline.
     * @param string $type             The type of construct this name is for.
     *                                 Either 'name', 'function' or 'constant'.
     * @param array  $useStatements    The use statements which may be applicable.
     *                                 Expects a multi-level array with the combined output of the
     *                                 {@see \PHPCSUtils\Utils\splitImportUseStatement()} method
     *                                 (or the output of the {@see \PHPCSUtils\Utils\splitAndMergeImportUseStatement()}
     *                                 method) for all potentially relevant import use statements.
     *                                 All expected keys - 'name', 'function' and 'constant' - should
     *                                 be present.
     * @param string $currentNamespace The namespace the name was found in.
     *                                 This can be an empty string to indicate global namespace.
     *                                 If the calling sniff does not keep track of this to begin with, the
     *                                 {@see \PHPCSUtils\Utils\Namespaces::determineNamespace()} method
     *                                 should be used to retrieve it and the result passed to this method.
     *
     * @return string|false The fully qualified name of the construct starting with a leading backslash
     *                      or FALSE if the fully qualified name could not (reliably) be determined,
     *                      like when a global function is used and there is no import `use` statement
     *                      for the function.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException When any of the input passed is invalid.
     */
    public static function resolveName($name, $type, $useStatements, $currentNamespace)
    {
        if (\is_string($name) === false || \ltrim($name, '?\\') === '') {
            throw new RuntimeException('Invalid input: $name must be a non-empty string');
        }

        if ($type !== 'name' && $type !== 'function' && $type !== 'const') {
            throw new RuntimeException('Invalid input: $type must be either "name", "function" or "const"');
        }

        if (\is_array($useStatements) === false
            || isset($useStatements['name'], $useStatements['function'], $useStatements['const']) === false
        ) {
            throw new RuntimeException(
                'Invalid input: $useStatements must be an array with the top-level keys "name", "function" and "const"'
            );
        }

        if (\is_string($currentNamespace) === false) {
            throw new RuntimeException('Invalid input: $currentNamespace must be a string (empty string allowed)');
        }

        // Fault tolerance: Trim off potential nullability indicator from type declarations.
        $name = \ltrim($name, '?');

        $nameLC = \strtolower($name);
        if ($nameLC === 'self' || $nameLC === 'static' || $nameLC === 'parent') {
            // Impossible to determine reliably which class will be invoked.
            return false;
        }

        if ($name[0] === '\\') {
            // Name is already fully qualified.
            return $name;
        }

        if (\stripos($name, 'namespace\\') === 0) {
            // Namespace operator found.
            $nameStripped = \substr($name, 9);

            if (empty($currentNamespace)) {
                return $nameStripped;
            }

            return '\\' . $currentNamespace . $nameStripped;
        }

        $parts = \explode('\\', $name);
        $first = \array_shift($parts);

        /*
         * Classes, interfaces, traits and generally imported namespaces - case insensitive check.
         */
        if ($type === 'name') {
            $foundUse = self::arrayKeyToValueCaseInsensitive($first, $useStatements[$type]);
            if (empty($foundUse) === false) {
                // Found an applicable import use statement.
                if (empty($parts)) {
                    return '\\' . $foundUse;
                }

                return '\\' . $foundUse . '\\' . \implode('\\', $parts);
            }

            // Non-fully qualified, non-imported class names always take the current namespace.
            if (empty($currentNamespace)) {
                return '\\' . $name;
            }

            return '\\' . $currentNamespace . '\\' . $name;
        }

        /*
         * Functions and constants.
         */
        if (empty($parts)) {
            // Unqualified function - case insensitive check.
            if ($type === 'function') {
                $foundUse = self::arrayKeyToValueCaseInsensitive($first, $useStatements[$type]);
                if (empty($foundUse) === false) {
                    return '\\' . $foundUse;
                }
            }

            // Unqualified constant - case sensitive check.
            if ($type === 'const' && isset($useStatements[$type][$name]) === true) {
                return '\\' . $useStatements[$type][$name];
            }

            if (empty($currentNamespace)) {
                return '\\' . $name;
            }

            /*
             * Undetermined. This may be a function/constant in the current namespace, but
             * could also fall-through to the global namespace.
             */
            return false;
        }

        /*
         * Partially qualified function/constant.
         * This may be a function/constant in an imported namespace.
         */
        $foundUse = self::arrayKeyToValueCaseInsensitive($first, $useStatements['name']);
        if (empty($foundUse) === false) {
             return '\\' . $foundUse . '\\' . \implode('\\', $parts);
        }

        if (empty($currentNamespace)) {
            return '\\' . $name;
        }

        // Undetermined, possibly fall-through.
        return false;
    }

    /**
     * Retrieve a value from an array based on a "case-insensitive" key comparison.
     *
     * This function respects the PHP rules about case-sensitivity of identifier names.
     *
     * @since 1.0.0
     *
     * @param string $targetKey The key to match.
     * @param array  $array     The array.
     *
     * @return mixed The array value corresponding to the given $targetKey or NULL when
     *               the key could not be matched.
     */
    private static function arrayKeyToValueCaseInsensitive($targetKey, $array)
    {
        if (\preg_match('`^[a-z0-9_]+$`i', $targetKey) === 1) {
            // Looks like this string is all ascii, using isset will be fine.
            $targetKeyLC = \strtolower($targetKey);
            $arrayLC     = \array_change_key_case($array, \CASE_LOWER);

            if (isset($arrayLC[$targetKeyLC]) === true) {
                return $arrayLC[$targetKeyLC];
            }

            return null;
        }

        /*
         * Ok, so this name contains non-ascii characters.
         */
        foreach ($array as $key => $value) {
            if (NamingConventions::isEqual($targetKey, $key) === true) {
                return $value;
            }
        }

        return null;
    }

    /**
     * Resolve a T_SELF token to the fully qualified name of the current class/interface/trait.
     *
     * Note: User beware! This is imprecise as when `self` is used, it may invoke methods/properties
     * from a (grand-)parent if these have not been overloaded.
     *
     * To support PHPCS < 2.8.0, passing a T_STRING token with as content `self` will also be accepted.
     * {@link https://github.com/squizlabs/php_codesniffer/issues/1245 See upstream bug PHPCS #1245}
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile        The file being scanned.
     * @param int                   $stackPtr         The position of a T_SELF token.
     * @param string|null           $currentNamespace Optional. The namespace the name was found in.
     *                                                This can be an empty string to indicate global namespace.
     *                                                This is an efficiency tweak to prevent duplicate function
     *                                                calls in case the calling sniff already keeps track of this.
     *
     * @return string|false The fully qualified name of the current class-like context starting
     *                      with a leading backslash; or FALSE in case of a parse error.
     *                      The FQN may be an empty string when `self` is used within an anonymous class.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is not of
     *                                                      type T_SELF or doesn't exist.
     */
    public static function resolveSelf(File $phpcsFile, $stackPtr, $currentNamespace = null)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || ($tokens[$stackPtr]['code'] !== \T_SELF && $tokens[$stackPtr]['code'] !== \T_STRING)
            || ($tokens[$stackPtr]['code'] === \T_STRING && $tokens[$stackPtr]['content'] !== 'self')
        ) {
            throw new RuntimeException('$stackPtr must be of type T_SELF');
        }

        $ooStruct = Conditions::getLastCondition($phpcsFile, $stackPtr, BCTokens::ooScopeTokens());
        if ($ooStruct === false) {
            // Parse error. Use of 'self' outside class context.
// TODO: check if I should make allowance for closures here which can be declared outside class context
// and then bound to one.
// Then again I wouldn't be able to resolve self in that case.
            return false;
        }

        $ooName = ObjectDeclarations::getName($phpcsFile, $ooStruct);
        if (empty($ooName)) {
            return '';
        }

        if (isset($currentNamespace) === false) {
            $currentNamespace = Namespaces::determineNamespace($phpcsFile, $stackPtr);
        }

        if (empty($currentNamespace)) {
            return '\\' . $ooName;
        }

        return '\\' . $currentNamespace . '\\' . $ooName;
    }

    /**
     * Retrieve the class name as used for a new class instantiation.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of a T_NEW token.
     *
     * @return string|false Class name or hierarchy keyword, or FALSE in case of a parse error or variable name.
     *                      Returns an empty string when this is the instantiation of an anonymous class.
     *                      The returned value may contain the `namespace` keyword if used as an operator.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is not of
     *                                                      type T_NEW or doesn't exist.
     */
    public static function getNameFromNew(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== \T_NEW) {
            throw new RuntimeException('$stackPtr must be of type T_NEW');
        }

        $endTokens = [
			\T_OPEN_PARENTHESIS   => \T_OPEN_PARENTHESIS,
			\T_OPEN_CURLY_BRACKET => \T_OPEN_CURLY_BRACKET,
			\T_OPEN_SHORT_ARRAY   => \T_OPEN_SHORT_ARRAY,
            \T_COLON              => \T_COLON,
            \T_COMMA              => \T_COMMA,
            \T_SEMICOLON          => \T_SEMICOLON,
            \T_CLOSE_PARENTHESIS  => \T_CLOSE_PARENTHESIS,
//            \T_CLOSE_SQUARE_BRACKET,
			\T_CLOSE_SHORT_ARRAY  => \T_CLOSE_SHORT_ARRAY,
            \T_CLOSE_TAG          => \T_CLOSE_TAG,
            \T_INLINE_THEN        => \T_INLINE_THEN,
            \T_INLINE_ELSE        => \T_INLINE_ELSE,
		];
        $endTokens += BCTokens::comparisonTokens();
        $endTokens += BCTokens::operators();
        $endTokens += BCTokens::booleanOperators();

        return self::getNameAfterKeyword($phpcsFile, $stackPtr, $endTokens);
    }

    /**
     * Retrieve the class/interface/trait name as used in an instanceof comparison.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of a T_INSTANCEOF token.
     *
     * @return string|false Class name or hierarchy keyword, or FALSE in case of a parse error or variable name.
     *                      The returned value may contain the `namespace` keyword if used as an operator.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is not of
     *                                                      type T_INSTANCEOF or doesn't exist.
     */
    public static function getNameFromInstanceOf(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== \T_INSTANCEOF) {
            throw new RuntimeException('$stackPtr must be of type T_INSTANCEOF');
        }

        $endTokens  = [
            \T_COLON             => \T_COLON,
            \T_COMMA             => \T_COMMA,
            \T_SEMICOLON         => \T_SEMICOLON,
            \T_CLOSE_PARENTHESIS => \T_CLOSE_PARENTHESIS,
			\T_CLOSE_SHORT_ARRAY => \T_CLOSE_SHORT_ARRAY,
            \T_CLOSE_TAG         => \T_CLOSE_TAG,
            \T_INLINE_THEN       => \T_INLINE_THEN,
            \T_INLINE_ELSE       => \T_INLINE_ELSE,
        ];
        $endTokens += BCTokens::comparisonTokens();
        $endTokens += BCTokens::operators();
        $endTokens += BCTokens::booleanOperators();

        $name = self::getNameAfterKeyword($phpcsFile, $stackPtr, $endTokens);
        if ($name !== false) {
            return $name;
        }

        // Rare, but this may be a class name text string.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false
            || ($tokens[$next]['code'] !== \T_NS_C
                && $tokens[$next]['code'] !== \T_CONSTANT_ENCAPSED_STRING)
        ) {
            return false;
        }

        $name = '';
        if ($tokens[$next]['code'] === \T_NS_C) {
            // For the purposes of name resolution, change it to namespace operator.
            $name = 'namespace';
        } else {
            $name = TextStrings::stripQuotes($tokens[$next]['content']);
        }

        $lastTokenCode = $tokens[$next]['code'];
        for ($next = ($next + 1); $next < $phpcsFile->numTokens; $next++) {
            if (isset(Tokens::$emptyTokens[$tokens[$next]['code']]) === true) {
                continue;
            }

            if (isset($endTokens[$tokens[$next]['code']]) === true) {
                break;
            }

            if ($tokens[$next]['code'] === \T_STRING_CONCAT
                && $lastTokenCode !== \T_STRING_CONCAT
            ) {
                $lastTokenCode = $tokens[$next]['code'];
                continue;
            }

            if ($tokens[$next]['code'] === \T_CONSTANT_ENCAPSED_STRING
                && $lastTokenCode !== \T_CONSTANT_ENCAPSED_STRING
            ) {
                $name         .= TextStrings::stripQuotes($tokens[$next]['content']);
                $lastTokenCode = $tokens[$next]['code'];
                continue;
            }

            // If we've reached this point, we encountered an unexpected token, so probably a variable name.
            return false;
        }

        return $name;
    }

    /**
     * Retrieve a identifier name as used after a specific keyword.
     *
     * @since 1.0.0-alpha4
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of a keyword token.
     * @param int|string[]          $endTokens Array of tokens constants which should be considered
     *                                         to end the statement/name.
     *
     * @return string|false Class name or hierarchy keyword, or FALSE in case of a parse error or variable name.
     *                      The returned value may contain the `namespace` keyword if used as an operator.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr doesn't exist.
     */
    public static function getNameAfterKeyword(File $phpcsFile, $stackPtr, $endTokens)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false) {
            throw new RuntimeException('$stackPtr not found in this file');
        }

        $end = $phpcsFile->findNext($endTokens, ($stackPtr + 1), null, false, null, true);
        if ($end === false) {
            return false;
        }

        $name            = '';
        $lastTokenCode   = null;
        $tokenMustBeLast = false;
        for ($i = ($stackPtr + 1); $i < $end; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            if (isset(Collections::$OOHierarchyKeywords[$tokens[$i]['code']]) === true
                // Work around tokenizer peculiarity where `static` after instanceof is not tokenized as T_STATIC.
                || ($tokens[$i]['code'] === \T_STRING && $tokens[$i]['content'] === 'static')
            ) {
                if ($name === '') {
                    $name           .= $tokens[$i]['content'];
                    $lastTokenCode   = $tokens[$i]['code'];
                    $tokenMustBeLast = true;
                    continue;
                }

                // Hierarchy keywords are only "names" if it's the first and only token found.
                return false;
            }

            if ($tokens[$i]['code'] === \T_ANON_CLASS) {
                // Allow for `new class() {}`.
                if ($name === '') {
                    $lastTokenCode   = $tokens[$i]['code'];
                    $tokenMustBeLast = true;
                    continue;
                }

                // Anon classes are only "names" if it's the first and only token found.
                return false;
            }

            if ($tokens[$i]['code'] === \T_NAMESPACE) {
                if ($name === '') {
                    $name         .= $tokens[$i]['content'];
                    $lastTokenCode = $tokens[$i]['code'];
                    continue;
                }

                // Namespace operator is only valid as part of a "name" if it's the first token found.
                return false;
            }

            if ($tokens[$i]['code'] === \T_NS_SEPARATOR || $tokens[$i]['code'] === \T_STRING) {
                // Do some error prevention.
                if (isset($lastTokenCode) === true) {
                    if ($tokens[$i]['code'] === $lastTokenCode) {
                        // Parse error.
                        return false;
                    }

                    if ($tokenMustBeLast === true) {
                        // Previous token flagged as "must be last". Parse error.
                        return false;
                    }

                    if ($lastTokenCode === \T_NAMESPACE && $tokens[$i]['code'] === \T_STRING) {
                        // Parse error.
                        return false;
                    }
                }

                $name         .= $tokens[$i]['content'];
                $lastTokenCode = $tokens[$i]['code'];
                continue;
            }

            if ($tokens[$i]['code'] === \T_DOUBLE_COLON) {
                // Allow for Name::class.
                $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), $end, true);
                if ($next === false) {
                    // Parse error.
                    return false;
                }

                /*
                 * Look at the content instead of the code to work around a tokenizer issue
                 * in PHPCS < 3.4.3.
                 */
                if ($tokens[$next]['content'] === 'class') {
                    $lastTokenCode   = $tokens[$next]['code'];
                    $tokenMustBeLast = true;
                    $i               = $next;
                    continue;
                }
            }

            // Unexpected token encountered. This must be a dynamic name, not a static one.
            return false;
        }

        if (isset($lastTokenCode) === false) {
            // No usable name found.
            return false;
        }

        return $name;
    }

    /**
     * Retrieve the class/interface/trait name for static usage of a class.
     *
     * This can be a call to a method, the use of a property or constant; or the use of the magic ::class constant.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of a T_DOUBLE_COLON token.
     *
     * @return string|false Class name or hierarchy keyword, or FALSE in case of a parse error
     *                      or variable name.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is not of
     *                                                      type T_DOUBLE_COLON or doesn't exist.
     */
    public static function getNameFromDoubleColon(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== \T_DOUBLE_COLON) {
            throw new RuntimeException('$stackPtr must be of type T_DOUBLE_COLON');
        }

        // No need to check for false, at the very least there will be a PHP open tag before it.
        $prev = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if (isset(Collections::$OOHierarchyKeywords[$tokens[$prev]['code']]) === true) {
            return $tokens[$prev]['content'];
        }

        if ($tokens[$prev]['code'] !== \T_STRING) {
            // Probably a variable.
            return false;
        }

        $parts    = [$tokens[$prev]['content']];
        $lastCode = $tokens[$prev]['code'];
        for ($i = ($prev - 1); $i >= 0; $i--) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            if (isset(Collections::$OONameTokens[$tokens[$i]['code']]) === false
                || $tokens[$i]['code'] === $lastCode
            ) {
                /*
                 * Either a token which can't exist within a name or two of the same tokens next to each other,
                 * which can't happen, so the previous token was the beginning.
                 */
                break;
            }

            /*
             * Work around missing tokenizer backfill for `yield` on PHP 5.4 with PHPCS < 3.1.0
             * and `yield from` on PHP < 7.0 with PHPCS 3.1.0.
             * Refs:
             * - https://github.com/squizlabs/PHP_CodeSniffer/issues/1513
             * - https://github.com/squizlabs/PHP_CodeSniffer/pull/1524
             */
            if ($tokens[$i]['code'] === \T_STRING
                && ($tokens[$i]['content'] === 'yield' || $tokens[$i]['content'] === 'from')
            ) {
                break;
            }

            $parts[]  = $tokens[$i]['content'];
            $lastCode = $tokens[$i]['code'];
        }

        return \implode('', \array_reverse($parts));
    }
}
