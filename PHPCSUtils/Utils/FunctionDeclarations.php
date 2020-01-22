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
use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Conditions;
use PHPCSUtils\Utils\GetTokensAsString;
use PHPCSUtils\Utils\ObjectDeclarations;
use PHPCSUtils\Utils\Scopes;

/**
 * Utility functions for use when examining function declaration statements.
 *
 * @since 1.0.0 The `getProperties()` and the `getParameters()` methods are
 *              based on and inspired by respectively the `getMethodProperties()`
 *              and `getMethodParameters()` methods in the PHPCS native `File` class.
 *              Also see {@see \PHPCSUtils\BackCompat\BCFile}.
 */
class FunctionDeclarations
{

    /**
     * A list of all PHP magic functions.
     *
     * The array keys are the function names, the values as well, but without the double underscore.
     *
     * The function names are listed in lowercase as function names in PHP are case-insensitive
     * and comparisons against this list should therefore always be done in a case-insensitive manner.
     *
     * @since 1.0.0
     *
     * @var array <string> => <string>
     */
    public static $magicFunctions = [
        '__autoload' => 'autoload',
    ];

    /**
     * A list of all PHP magic methods.
     *
     * The array keys are the method names, the values as well, but without the double underscore.
     *
     * The method names are listed in lowercase as function names in PHP are case-insensitive
     * and comparisons against this list should therefore always be done in a case-insensitive manner.
     *
     * @since 1.0.0
     *
     * @var array <string> => <string>
     */
    public static $magicMethods = [
        '__construct'   => 'construct',
        '__destruct'    => 'destruct',
        '__call'        => 'call',
        '__callstatic'  => 'callstatic',
        '__get'         => 'get',
        '__set'         => 'set',
        '__isset'       => 'isset',
        '__unset'       => 'unset',
        '__sleep'       => 'sleep',
        '__wakeup'      => 'wakeup',
        '__tostring'    => 'tostring',
        '__set_state'   => 'set_state',
        '__clone'       => 'clone',
        '__invoke'      => 'invoke',
        '__debuginfo'   => 'debuginfo', // PHP 5.6.
        '__serialize'   => 'serialize', // PHP 7.4.
        '__unserialize' => 'unserialize', // PHP 7.4.
    ];

    /**
     * A list of all PHP native non-magic methods starting with a double underscore.
     *
     * These come from PHP modules such as SOAPClient.
     *
     * The array keys are the method names, the values the name of the PHP extension containing
     * the function.
     *
     * The method names are listed in lowercase as function names in PHP are case-insensitive
     * and comparisons against this list should therefore always be done in a case-insensitive manner.
     *
     * @since 1.0.0
     *
     * @var array <string> => <string>
     */
    public static $methodsDoubleUnderscore = [
        '__dorequest'              => 'SOAPClient',
        '__getcookies'             => 'SOAPClient',
        '__getfunctions'           => 'SOAPClient',
        '__getlastrequest'         => 'SOAPClient',
        '__getlastrequestheaders'  => 'SOAPClient',
        '__getlastresponse'        => 'SOAPClient',
        '__getlastresponseheaders' => 'SOAPClient',
        '__gettypes'               => 'SOAPClient',
        '__setcookie'              => 'SOAPClient',
        '__setlocation'            => 'SOAPClient',
        '__setsoapheaders'         => 'SOAPClient',
        '__soapcall'               => 'SOAPClient',
    ];

    /**
     * Returns the declaration name for a function.
     *
     * Alias for the {@see \PHPCSUtils\Utils\ObjectDeclarations::getName()} method.
     *
     * @codeCoverageIgnore
     *
     * @see \PHPCSUtils\BackCompat\BCFile::getDeclarationName() Original function.
     * @see \PHPCSUtils\Utils\ObjectDeclarations::getName()     PHPCSUtils native improved version.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the declaration token
     *                                               which declared the function.
     *
     * @return string|null The name of the function; or NULL if the passed token doesn't exist,
     *                     the function is anonymous or in case of a parse error/live coding.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not of type
     *                                                      T_FUNCTION, T_CLASS, T_TRAIT, or T_INTERFACE.
     */
    public static function getName(File $phpcsFile, $stackPtr)
    {
        return ObjectDeclarations::getName($phpcsFile, $stackPtr);
    }

    /**
     * Retrieves the visibility and implementation properties of a method.
     *
     * The format of the return value is:
     * <code>
     *   array(
     *    'scope'                 => 'public', // Public, private, or protected
     *    'scope_specified'       => true,     // TRUE if the scope keyword was found.
     *    'return_type'           => '',       // The return type of the method.
     *    'return_type_token'     => integer,  // The stack pointer to the start of the return type
     *                                         // or FALSE if there is no return type.
     *    'return_type_end_token' => integer,  // The stack pointer to the end of the return type
     *                                         // or FALSE if there is no return type.
     *    'nullable_return_type'  => false,    // TRUE if the return type is nullable.
     *    'is_abstract'           => false,    // TRUE if the abstract keyword was found.
     *    'is_final'              => false,    // TRUE if the final keyword was found.
     *    'is_static'             => false,    // TRUE if the static keyword was found.
     *    'has_body'              => false,    // TRUE if the method has a body
     *   );
     * </code>
     *
     * Main differences with the PHPCS version:
     * - Bugs fixed:
     *   - Handling of PHPCS annotations.
     *   - `has_body` index could be set to `true` for functions without body in the case of
     *      parse errors or live coding.
     * - Defensive coding against incorrect calls to this method.
     * - More efficient checking whether a function has a body.
     * - New `return_type_end_token` (int|false) array index.
     *
     * @see \PHP_CodeSniffer\Files\File::getMethodProperties()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::getMethodProperties() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the function token to
     *                                               acquire the properties for.
     *
     * @return array
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_FUNCTION or a T_CLOSURE token.
     */
    public static function getProperties(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || ($tokens[$stackPtr]['code'] !== \T_FUNCTION
                && $tokens[$stackPtr]['code'] !== \T_CLOSURE)
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE');
        }

        if ($tokens[$stackPtr]['code'] === \T_FUNCTION) {
            $valid = Tokens::$methodPrefixes;
        } else {
            $valid = [\T_STATIC => \T_STATIC];
        }

        $valid += Tokens::$emptyTokens;

        $scope          = 'public';
        $scopeSpecified = false;
        $isAbstract     = false;
        $isFinal        = false;
        $isStatic       = false;

        for ($i = ($stackPtr - 1); $i > 0; $i--) {
            if (isset($valid[$tokens[$i]['code']]) === false) {
                break;
            }

            switch ($tokens[$i]['code']) {
                case \T_PUBLIC:
                    $scope          = 'public';
                    $scopeSpecified = true;
                    break;
                case \T_PRIVATE:
                    $scope          = 'private';
                    $scopeSpecified = true;
                    break;
                case \T_PROTECTED:
                    $scope          = 'protected';
                    $scopeSpecified = true;
                    break;
                case \T_ABSTRACT:
                    $isAbstract = true;
                    break;
                case \T_FINAL:
                    $isFinal = true;
                    break;
                case \T_STATIC:
                    $isStatic = true;
                    break;
            }
        }

        $returnType         = '';
        $returnTypeToken    = false;
        $returnTypeEndToken = false;
        $nullableReturnType = false;
        $hasBody            = false;

        if (isset($tokens[$stackPtr]['parenthesis_closer']) === true) {
            $scopeOpener = null;
            if (isset($tokens[$stackPtr]['scope_opener']) === true) {
                $scopeOpener = $tokens[$stackPtr]['scope_opener'];
            }

            for ($i = $tokens[$stackPtr]['parenthesis_closer']; $i < $phpcsFile->numTokens; $i++) {
                if ($i === $scopeOpener) {
                    // End of function definition.
                    $hasBody = true;
                    break;
                }

                if ($scopeOpener === null && $tokens[$i]['code'] === \T_SEMICOLON) {
                    // End of abstract/interface function definition.
                    break;
                }

                if ($tokens[$i]['type'] === 'T_NULLABLE'
                    // Handle nullable tokens in PHPCS < 2.8.0.
                    || (\defined('T_NULLABLE') === false && $tokens[$i]['code'] === \T_INLINE_THEN)
                ) {
                    $nullableReturnType = true;
                }

                if (isset(Collections::$returnTypeTokens[$tokens[$i]['code']]) === true) {
                    if ($returnTypeToken === false) {
                        $returnTypeToken = $i;
                    }

                    $returnType        .= $tokens[$i]['content'];
                    $returnTypeEndToken = $i;
                }
            }
        }

        if ($returnType !== '' && $nullableReturnType === true) {
            $returnType = '?' . $returnType;
        }

        return [
            'scope'                 => $scope,
            'scope_specified'       => $scopeSpecified,
            'return_type'           => $returnType,
            'return_type_token'     => $returnTypeToken,
            'return_type_end_token' => $returnTypeEndToken,
            'nullable_return_type'  => $nullableReturnType,
            'is_abstract'           => $isAbstract,
            'is_final'              => $isFinal,
            'is_static'             => $isStatic,
            'has_body'              => $hasBody,
        ];
    }

    /**
     * Retrieves the method parameters for the specified function token.
     *
     * Also supports passing in a USE token for a closure use group.
     *
     * The returned array will contain the following information for each parameter:
     *
     * <code>
     *   0 => array(
     *         'name'                => '$var',  // The variable name.
     *         'token'               => integer, // The stack pointer to the variable name.
     *         'content'             => string,  // The full content of the variable definition.
     *         'pass_by_reference'   => boolean, // Is the variable passed by reference?
     *         'reference_token'     => integer, // The stack pointer to the reference operator
     *                                           // or FALSE if the param is not passed by reference.
     *         'variable_length'     => boolean, // Is the param of variable length through use of `...` ?
     *         'variadic_token'      => integer, // The stack pointer to the ... operator
     *                                           // or FALSE if the param is not variable length.
     *         'type_hint'           => string,  // The type hint for the variable.
     *         'type_hint_token'     => integer, // The stack pointer to the start of the type hint
     *                                           // or FALSE if there is no type hint.
     *         'type_hint_end_token' => integer, // The stack pointer to the end of the type hint
     *                                           // or FALSE if there is no type hint.
     *         'nullable_type'       => boolean, // TRUE if the var type is nullable.
     *         'comma_token'         => integer, // The stack pointer to the comma after the param
     *                                           // or FALSE if this is the last param.
     *        )
     * </code>
     *
     * Parameters with default values have the following additional array indexes:
     *         'default'             => string,  // The full content of the default value.
     *         'default_token'       => integer, // The stack pointer to the start of the default value.
     *         'default_equal_token' => integer, // The stack pointer to the equals sign.
     *
     * Main differences with the PHPCS version:
     * - Defensive coding against incorrect calls to this method.
     * - More efficient checking whether a T_USE token is a closure use.
     * - More efficient and more stable looping of the default value.
     *
     * @see \PHP_CodeSniffer\Files\File::getMethodParameters()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::getMethodParameters() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the function token
     *                                               to acquire the parameters for.
     *
     * @return array
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified $stackPtr is not of
     *                                                      type T_FUNCTION, T_CLOSURE, or T_USE.
     */
    public static function getParameters(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || ($tokens[$stackPtr]['code'] !== \T_FUNCTION
                && $tokens[$stackPtr]['code'] !== \T_CLOSURE
                && $tokens[$stackPtr]['code'] !== \T_USE)
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_USE');
        }

        if ($tokens[$stackPtr]['code'] === \T_USE) {
            $opener = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            if ($opener === false
                || $tokens[$opener]['code'] !== \T_OPEN_PARENTHESIS
                || isset($tokens[$opener]['parenthesis_owner']) === true
            ) {
                throw new RuntimeException('$stackPtr was not a valid T_USE');
            }
        } else {
            if (isset($tokens[$stackPtr]['parenthesis_opener']) === false) {
                // Live coding or syntax error, so no params to find.
                return [];
            }

            $opener = $tokens[$stackPtr]['parenthesis_opener'];
        }

        if (isset($tokens[$opener]['parenthesis_closer']) === false) {
            // Live coding or syntax error, so no params to find.
            return [];
        }

        $closer = $tokens[$opener]['parenthesis_closer'];

        $vars             = [];
        $currVar          = null;
        $paramStart       = ($opener + 1);
        $defaultStart     = null;
        $equalToken       = null;
        $paramCount       = 0;
        $passByReference  = false;
        $referenceToken   = false;
        $variableLength   = false;
        $variadicToken    = false;
        $typeHint         = '';
        $typeHintToken    = false;
        $typeHintEndToken = false;
        $nullableType     = false;

        for ($i = $paramStart; $i <= $closer; $i++) {
            // Changed from checking 'code' to 'type' to allow for T_NULLABLE not existing in PHPCS < 2.8.0.
            switch ($tokens[$i]['type']) {
                case 'T_BITWISE_AND':
                    $passByReference = true;
                    $referenceToken  = $i;
                    break;

                case 'T_VARIABLE':
                    $currVar = $i;
                    break;

                case 'T_ELLIPSIS':
                    $variableLength = true;
                    $variadicToken  = $i;
                    break;

                case 'T_ARRAY_HINT': // PHPCS < 3.3.0.
                case 'T_CALLABLE':
                case 'T_SELF':
                case 'T_PARENT':
                case 'T_STATIC': // Self and parent are valid, static invalid, but was probably intended as type hint.
                case 'T_STRING':
                case 'T_NS_SEPARATOR':
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint        .= $tokens[$i]['content'];
                    $typeHintEndToken = $i;
                    break;

                case 'T_NULLABLE':
                case 'T_INLINE_THEN': // PHPCS < 2.8.0.
                    $nullableType     = true;
                    $typeHint        .= $tokens[$i]['content'];
                    $typeHintEndToken = $i;
                    break;

                case 'T_CLOSE_PARENTHESIS':
                case 'T_COMMA':
                    // If it's null, then there must be no parameters for this
                    // method.
                    if ($currVar === null) {
                        continue 2;
                    }

                    $vars[$paramCount]            = [];
                    $vars[$paramCount]['token']   = $currVar;
                    $vars[$paramCount]['name']    = $tokens[$currVar]['content'];
                    $vars[$paramCount]['content'] = \trim(
                        GetTokensAsString::normal($phpcsFile, $paramStart, ($i - 1))
                    );

                    if ($defaultStart !== null) {
                        $vars[$paramCount]['default']             = \trim(
                            GetTokensAsString::normal($phpcsFile, $defaultStart, ($i - 1))
                        );
                        $vars[$paramCount]['default_token']       = $defaultStart;
                        $vars[$paramCount]['default_equal_token'] = $equalToken;
                    }

                    $vars[$paramCount]['pass_by_reference']   = $passByReference;
                    $vars[$paramCount]['reference_token']     = $referenceToken;
                    $vars[$paramCount]['variable_length']     = $variableLength;
                    $vars[$paramCount]['variadic_token']      = $variadicToken;
                    $vars[$paramCount]['type_hint']           = $typeHint;
                    $vars[$paramCount]['type_hint_token']     = $typeHintToken;
                    $vars[$paramCount]['type_hint_end_token'] = $typeHintEndToken;
                    $vars[$paramCount]['nullable_type']       = $nullableType;

                    if ($tokens[$i]['code'] === \T_COMMA) {
                        $vars[$paramCount]['comma_token'] = $i;
                    } else {
                        $vars[$paramCount]['comma_token'] = false;
                    }

                    // Reset the vars, as we are about to process the next parameter.
                    $currVar          = null;
                    $paramStart       = ($i + 1);
                    $defaultStart     = null;
                    $equalToken       = null;
                    $passByReference  = false;
                    $referenceToken   = false;
                    $variableLength   = false;
                    $variadicToken    = false;
                    $typeHint         = '';
                    $typeHintToken    = false;
                    $typeHintEndToken = false;
                    $nullableType     = false;

                    $paramCount++;
                    break;

                case 'T_EQUAL':
                    $defaultStart = $phpcsFile->findNext(Tokens::$emptyTokens, ($i + 1), null, true);
                    $equalToken   = $i;

                    // Skip past everything in the default value before going into the next switch loop.
                    for ($j = ($i + 1); $j <= $closer; $j++) {
                        // Skip past array()'s et al as default values.
                        if (isset($tokens[$j]['parenthesis_opener'], $tokens[$j]['parenthesis_closer'])) {
                            $j = $tokens[$j]['parenthesis_closer'];

                            if ($j === $closer) {
                                // Found the end of the parameter.
                                break;
                            }

                            continue;
                        }

                        // Skip past short arrays et al as default values.
                        if (isset($tokens[$j]['bracket_opener'])) {
                            $j = $tokens[$j]['bracket_closer'];
                            continue;
                        }

                        if ($tokens[$j]['code'] === \T_COMMA) {
                            break;
                        }
                    }

                    $i = ($j - 1);
                    break;
            }
        }

        return $vars;
    }

    /**
     * Checks if a given function is a PHP magic function.
     *
     * @todo Add check for the function declaration being namespaced!
     *
     * @see \PHPCSUtils\Utils\FunctionDeclaration::isMagicFunctionName() For when you already know the name of the
     *                                                                   function and scope checking is done in the
     *                                                                   sniff.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The T_FUNCTION token to check.
     *
     * @return bool
     */
    public static function isMagicFunction(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== \T_FUNCTION) {
            return false;
        }

        if (Conditions::hasCondition($phpcsFile, $stackPtr, BCTokens::ooScopeTokens()) === true) {
            return false;
        }

        $name = self::getName($phpcsFile, $stackPtr);
        return self::isMagicFunctionName($name);
    }

    /**
     * Verify if a given function name is the name of a PHP magic function.
     *
     * @since 1.0.0
     *
     * @param string $name The full function name.
     *
     * @return bool
     */
    public static function isMagicFunctionName($name)
    {
        $name = \strtolower($name);
        return (isset(self::$magicFunctions[$name]) === true);
    }

    /**
     * Checks if a given function is a PHP magic method.
     *
     * @see \PHPCSUtils\Utils\FunctionDeclaration::isMagicMethodName() For when you already know the name of the
     *                                                                 method and scope checking is done in the
     *                                                                 sniff.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The T_FUNCTION token to check.
     *
     * @return bool
     */
    public static function isMagicMethod(File $phpcsFile, $stackPtr)
    {
        if (Scopes::isOOMethod($phpcsFile, $stackPtr) === false) {
            return false;
        }

        $name = self::getName($phpcsFile, $stackPtr);
        return self::isMagicMethodName($name);
    }

    /**
     * Verify if a given function name is the name of a PHP magic method.
     *
     * @since 1.0.0
     *
     * @param string $name The full function name.
     *
     * @return bool
     */
    public static function isMagicMethodName($name)
    {
        $name = \strtolower($name);
        return (isset(self::$magicMethods[$name]) === true);
    }

    /**
     * Checks if a given function is a PHP native double underscore method.
     *
     * @see \PHPCSUtils\Utils\FunctionDeclaration::isPHPDoubleUnderscoreMethodName() For when you already know the
     *                                                                               name of the method and scope
     *                                                                               checking is done in the sniff.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The T_FUNCTION token to check.
     *
     * @return bool
     */
    public static function isPHPDoubleUnderscoreMethod(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== \T_FUNCTION) {
            return false;
        }

        $scopePtr = Scopes::validDirectScope($phpcsFile, $stackPtr, BCTokens::ooScopeTokens());
        if ($scopePtr === false) {
            return false;
        }

        /*
         * If this is a class, make sure it extends something, as otherwise, the methods
         * still can't be overloads for the SOAPClient methods.
         * For a trait/interface we don't know the concrete implementation context, so skip
         * this check.
         */
        if ($tokens[$scopePtr]['code'] === \T_CLASS || $tokens[$scopePtr]['code'] === \T_ANON_CLASS) {
            $extends = ObjectDeclarations::findExtendedClassName($phpcsFile, $scopePtr);
            if ($extends === false) {
                return false;
            }
        }

        $name = self::getName($phpcsFile, $stackPtr);
        return self::isPHPDoubleUnderscoreMethodName($name);
    }

    /**
     * Verify if a given function name is the name of a PHP native double underscore method.
     *
     * @since 1.0.0
     *
     * @param string $name The full function name.
     *
     * @return bool
     */
    public static function isPHPDoubleUnderscoreMethodName($name)
    {
        $name = \strtolower($name);
        return (isset(self::$methodsDoubleUnderscore[$name]) === true);
    }

    /**
     * Checks if a given function is a magic method or a PHP native double underscore method.
     *
     * @see \PHPCSUtils\Utils\FunctionDeclaration::isSpecialMethodName() For when you already know the name of the
     *                                                                   method and scope checking is done in the
     *                                                                   sniff.
     *
     * @since 1.0.0
     *
     * {@internal Not the most efficient way of checking this, but less efficient ways will get
     *            less reliable results or introduce a lot of code duplication.}
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  The T_FUNCTION token to check.
     *
     * @return bool
     */
    public static function isSpecialMethod(File $phpcsFile, $stackPtr)
    {
        if (self::isMagicMethod($phpcsFile, $stackPtr) === true) {
            return true;
        }

        if (self::isPHPDoubleUnderscoreMethod($phpcsFile, $stackPtr) === true) {
            return true;
        }

        return false;
    }

    /**
     * Verify if a given function name is the name of a magic method or a PHP native double underscore method.
     *
     * @since 1.0.0
     *
     * @param string $name The full function name.
     *
     * @return bool
     */
    public static function isSpecialMethodName($name)
    {
        $name = \strtolower($name);
        return (isset(self::$magicMethods[$name]) === true || isset(self::$methodsDoubleUnderscore[$name]) === true);
    }
}
