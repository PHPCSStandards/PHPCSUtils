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
use PHPCSUtils\Utils\Scopes;

/**
 * Utility functions for use when examining variables.
 *
 * @since 1.0.0 The `getMemberProperties()` method is based on and inspired by
 *              the method of the same name in the PHPCS native `File` class.
 *              Also see {@see \PHPCSUtils\BackCompat\BCFile}.
 */
class Variables
{

    /**
     * Retrieve the visibility and implementation properties of a class member var.
     *
     * The format of the return value is:
     *
     * <code>
     *   array(
     *    'scope'           => string,  // Public, private, or protected.
     *    'scope_specified' => boolean, // TRUE if the scope was explicitly specified.
     *    'is_static'       => boolean, // TRUE if the static keyword was found.
     *    'type'            => string,  // The type of the var (empty if no type specified).
     *    'type_token'      => integer, // The stack pointer to the start of the type
     *                                  // or FALSE if there is no type.
     *    'type_end_token'  => integer, // The stack pointer to the end of the type
     *                                  // or FALSE if there is no type.
     *    'nullable_type'   => boolean, // TRUE if the type is nullable.
     *   );
     * </code>
     *
     * Main differences with the PHPCS version:
     * - Removed the parse error warning for properties in interfaces.
     *   This will now throw the same "$stackPtr is not a class member var" runtime exception as
     *   other non-property variables passed to the method.
     * - Defensive coding against incorrect calls to this method.
     *
     * @see \PHP_CodeSniffer\Files\File::getMemberProperties()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::getMemberProperties() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position in the stack of the T_VARIABLE token to
     *                                               acquire the properties for.
     *
     * @return array
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_VARIABLE token, or if the position is not
     *                                                      a class member variable.
     */
    public static function getMemberProperties(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== \T_VARIABLE) {
            throw new RuntimeException('$stackPtr must be of type T_VARIABLE');
        }

        if (Scopes::isOOProperty($phpcsFile, $stackPtr) === false) {
            throw new RuntimeException('$stackPtr is not a class member var');
        }

        $valid = Collections::$propertyModifierKeywords + Tokens::$emptyTokens;

        $scope          = 'public';
        $scopeSpecified = false;
        $isStatic       = false;

        $startOfStatement = $phpcsFile->findPrevious(
            [
                \T_SEMICOLON,
                \T_OPEN_CURLY_BRACKET,
                \T_CLOSE_CURLY_BRACKET,
            ],
            ($stackPtr - 1)
        );

        for ($i = ($startOfStatement + 1); $i < $stackPtr; $i++) {
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
                case \T_STATIC:
                    $isStatic = true;
                    break;
            }
        }

        $type         = '';
        $typeToken    = false;
        $typeEndToken = false;
        $nullableType = false;

        if ($i < $stackPtr) {
            // We've found a type.
            for ($i; $i < $stackPtr; $i++) {
                if ($tokens[$i]['code'] === \T_VARIABLE) {
                    // Hit another variable in a group definition.
                    break;
                }

                if ($tokens[$i]['type'] === 'T_NULLABLE'
                    // Handle nullable property types in PHPCS < 3.5.0.
                    || $tokens[$i]['code'] === \T_INLINE_THEN
                ) {
                    $nullableType = true;
                }

                if (isset(Collections::$propertyTypeTokens[$tokens[$i]['code']]) === true) {
                    $typeEndToken = $i;
                    if ($typeToken === false) {
                        $typeToken = $i;
                    }

                    $type .= $tokens[$i]['content'];
                }
            }

            if ($type !== '' && $nullableType === true) {
                $type = '?' . $type;
            }
        }

        return [
            'scope'           => $scope,
            'scope_specified' => $scopeSpecified,
            'is_static'       => $isStatic,
            'type'            => $type,
            'type_token'      => $typeToken,
            'type_end_token'  => $typeEndToken,
            'nullable_type'   => $nullableType,
        ];
    }
}