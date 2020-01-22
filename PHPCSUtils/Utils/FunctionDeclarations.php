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
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\ObjectDeclarations;

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
     *    'scope'                => 'public', // Public, private, or protected
     *    'scope_specified'      => true,     // TRUE if the scope keyword was found.
     *    'return_type'          => '',       // The return type of the method.
     *    'return_type_token'    => integer,  // The stack pointer to the start of the return type
     *                                        // or FALSE if there is no return type.
     *    'nullable_return_type' => false,    // TRUE if the return type is nullable.
     *    'is_abstract'          => false,    // TRUE if the abstract keyword was found.
     *    'is_final'             => false,    // TRUE if the final keyword was found.
     *    'is_static'            => false,    // TRUE if the static keyword was found.
     *    'has_body'             => false,    // TRUE if the method has a body
     *   );
     * </code>
     *
     * Main differences with the PHPCS version:
     * - Bugs fixed:
     *   - Handling of PHPCS annotations.
     * - Defensive coding against incorrect calls to this method.
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
        $nullableReturnType = false;
        $hasBody            = true;

        if (isset($tokens[$stackPtr]['parenthesis_closer']) === true) {
            $scopeOpener = null;
            if (isset($tokens[$stackPtr]['scope_opener']) === true) {
                $scopeOpener = $tokens[$stackPtr]['scope_opener'];
            }

            for ($i = $tokens[$stackPtr]['parenthesis_closer']; $i < $phpcsFile->numTokens; $i++) {
                if (($scopeOpener === null && $tokens[$i]['code'] === \T_SEMICOLON)
                    || ($scopeOpener !== null && $i === $scopeOpener)
                ) {
                    // End of function definition.
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

                    $returnType .= $tokens[$i]['content'];
                }
            }

            $end     = $phpcsFile->findNext(
                [\T_OPEN_CURLY_BRACKET, \T_SEMICOLON],
                $tokens[$stackPtr]['parenthesis_closer']
            );
            $hasBody = $tokens[$end]['code'] === \T_OPEN_CURLY_BRACKET;
        }

        if ($returnType !== '' && $nullableReturnType === true) {
            $returnType = '?' . $returnType;
        }

        return [
            'scope'                => $scope,
            'scope_specified'      => $scopeSpecified,
            'return_type'          => $returnType,
            'return_type_token'    => $returnTypeToken,
            'nullable_return_type' => $nullableReturnType,
            'is_abstract'          => $isAbstract,
            'is_final'             => $isFinal,
            'is_static'            => $isStatic,
            'has_body'             => $hasBody,
        ];
    }

    /**
     * Returns the method parameters for the specified function token.
     *
     * Also supports passing in a USE token for a closure use group.
     *
     * Each parameter is in the following format:
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
     * Parameters with default values have an additional array indexs of:
     *         'default'             => string,  // The full content of the default value.
     *         'default_token'       => integer, // The stack pointer to the start of the default value.
     *         'default_equal_token' => integer, // The stack pointer to the equals sign.
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

        if ($tokens[$stackPtr]['code'] !== \T_FUNCTION
            && $tokens[$stackPtr]['code'] !== \T_CLOSURE
            && $tokens[$stackPtr]['code'] !== \T_USE
        ) {
            throw new RuntimeException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or T_USE');
        }

        if ($tokens[$stackPtr]['code'] === \T_USE) {
            $opener = $phpcsFile->findNext(\T_OPEN_PARENTHESIS, ($stackPtr + 1));
            if ($opener === false || isset($tokens[$opener]['parenthesis_owner']) === true) {
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
            // Check to see if this token has a parenthesis or bracket opener. If it does
            // it's likely to be an array which might have arguments in it. This
            // could cause problems in our parsing below, so lets just skip to the
            // end of it.
            if (isset($tokens[$i]['parenthesis_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $tokens[$i]['parenthesis_closer']) {
                    $i = ($tokens[$i]['parenthesis_closer'] + 1);
                }
            }

            if (isset($tokens[$i]['bracket_opener']) === true) {
                // Don't do this if it's the close parenthesis for the method.
                if ($i !== $tokens[$i]['bracket_closer']) {
                    $i = ($tokens[$i]['bracket_closer'] + 1);
                }
            }

            // Changed from checking 'code' to 'type' to allow for T_NULLABLE not existing in PHPCS < 2.8.0.
            switch ($tokens[$i]['type']) {
                case 'T_BITWISE_AND':
                    if ($defaultStart === null) {
                        $passByReference = true;
                        $referenceToken  = $i;
                    }
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
                    if ($typeHintToken === false) {
                        $typeHintToken = $i;
                    }

                    $typeHint        .= $tokens[$i]['content'];
                    $typeHintEndToken = $i;
                    break;

                case 'T_SELF':
                case 'T_PARENT':
                case 'T_STATIC':
                    // Self and parent are valid, static invalid, but was probably intended as type hint.
                    if (isset($defaultStart) === false) {
                        if ($typeHintToken === false) {
                            $typeHintToken = $i;
                        }

                        $typeHint        .= $tokens[$i]['content'];
                        $typeHintEndToken = $i;
                    }
                    break;

                case 'T_STRING':
                    // This is a string, so it may be a type hint, but it could
                    // also be a constant used as a default value.
                    $prevComma = false;
                    for ($t = $i; $t >= $opener; $t--) {
                        if ($tokens[$t]['code'] === \T_COMMA) {
                            $prevComma = $t;
                            break;
                        }
                    }

                    if ($prevComma !== false) {
                        $nextEquals = false;
                        for ($t = $prevComma; $t < $i; $t++) {
                            if ($tokens[$t]['code'] === \T_EQUAL) {
                                $nextEquals = $t;
                                break;
                            }
                        }

                        if ($nextEquals !== false) {
                            break;
                        }
                    }

                    if ($defaultStart === null) {
                        if ($typeHintToken === false) {
                            $typeHintToken = $i;
                        }

                        $typeHint        .= $tokens[$i]['content'];
                        $typeHintEndToken = $i;
                    }
                    break;

                case 'T_NS_SEPARATOR':
                    // Part of a type hint or default value.
                    if ($defaultStart === null) {
                        if ($typeHintToken === false) {
                            $typeHintToken = $i;
                        }

                        $typeHint        .= $tokens[$i]['content'];
                        $typeHintEndToken = $i;
                    }
                    break;

                case 'T_NULLABLE':
                case 'T_INLINE_THEN': // PHPCS < 2.8.0.
                    if ($defaultStart === null) {
                        $nullableType     = true;
                        $typeHint        .= $tokens[$i]['content'];
                        $typeHintEndToken = $i;
                    }
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
                        $phpcsFile->getTokensAsString($paramStart, ($i - $paramStart))
                    );

                    if ($defaultStart !== null) {
                        $vars[$paramCount]['default']             = \trim(
                            $phpcsFile->getTokensAsString($defaultStart, ($i - $defaultStart))
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
                    break;
            }
        }

        return $vars;
    }
}
