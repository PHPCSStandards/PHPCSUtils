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
use PHPCSUtils\Utils\GetTokensAsString;
use PHPCSUtils\Utils\Namespaces;
use PHPCSUtils\Utils\NamingConventions;
use PHPCSUtils\Utils\ObjectDeclarations;

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

        $endTokens = [\T_OPEN_PARENTHESIS, \T_SEMICOLON, \T_OPEN_CURLY_BRACKET];
        $end       = $phpcsFile->findNext($endTokens, ($stackPtr + 1), null, false, null, true);
        if ($end === false) {
            return false;
        }

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), $end, true);
        if ($next === false) {
            return false;
        }

        if ($tokens[$next]['code'] === \T_ANON_CLASS) {
            return '';
        }

        $allowed      = Collections::$OONameTokens + Collections::$OOHierarchyKeywords + Tokens::$emptyTokens;
        $undetermined = $phpcsFile->findNext($allowed, $next, $end, true);
        if ($undetermined !== false) {
            // Name could not be determined. Probably a (partially) variable name.
            return false;
        }

        return GetTokensAsString::noempties($phpcsFile, $next, ($end - 1));
    }

    /**
     * Retrieve the class name for static usage of a class.
     *
     * This can be a call to a method, the use of a property or constant; or the use of the magic ::class constant.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                   $stackPtr  The position of a T_DOUBLE_COLON token.
     *
     * @return string|false Class name or hierarchy keyword, or FALSE in case of a parse error
     *                       or variable name.
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

            $parts[]  = $tokens[$i]['content'];
            $lastCode = $tokens[$i]['code'];
        }

        return \implode('', \array_reverse($parts));
    }
}
