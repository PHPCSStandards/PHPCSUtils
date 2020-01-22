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

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\Parentheses;

/**
 * Utility functions for use when working with operators.
 *
 * @since 1.0.0 The `isReference()` method is based on and inspired by
 *              the method of the same name in the PHPCS native `File` class.
 *              Also see {@see \PHPCSUtils\BackCompat\BCFile}.
 */
class Operators
{

    /**
     * Determine if the passed token is a reference operator.
     *
     * @see \PHP_CodeSniffer\Files\File::isReference()   Original source.
     * @see \PHPCSUtils\BackCompat\BCFile::isReference() Cross-version compatible version of the original.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the T_BITWISE_AND token.
     *
     * @return bool TRUE if the specified token position represents a reference.
     *              FALSE if the token represents a bitwise operator.
     */
    public static function isReference(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['code'] !== \T_BITWISE_AND) {
            return false;
        }

        $tokenBefore = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);

        if ($tokens[$tokenBefore]['code'] === \T_FUNCTION) {
            // Function returns a reference.
            return true;
        }

        if ($tokens[$tokenBefore]['code'] === \T_DOUBLE_ARROW) {
            // Inside a foreach loop or array assignment, this is a reference.
            return true;
        }

        if ($tokens[$tokenBefore]['code'] === \T_AS) {
            // Inside a foreach loop, this is a reference.
            return true;
        }

        if (isset(BCTokens::assignmentTokens()[$tokens[$tokenBefore]['code']]) === true) {
            // This is directly after an assignment. It's a reference. Even if
            // it is part of an operation, the other tests will handle it.
            return true;
        }

        $tokenAfter = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);

        if ($tokens[$tokenAfter]['code'] === \T_NEW) {
            return true;
        }

        $lastOpener = Parentheses::getLastOpener($phpcsFile, $stackPtr);
        if ($lastOpener !== false) {
            $lastOwner = Parentheses::lastOwnerIn($phpcsFile, $stackPtr, [\T_FUNCTION, \T_CLOSURE]);
            if ($lastOwner !== false) {
                $params = FunctionDeclarations::getParameters($phpcsFile, $lastOwner);
                foreach ($params as $param) {
                    if ($param['pass_by_reference'] === true) {
                        // Function parameter declared to be passed by reference.
                        return true;
                    }
                }
            } elseif (isset($tokens[$lastOpener]['parenthesis_owner']) === false) {
                $prev = false;
                for ($t = ($lastOpener - 1); $t >= 0; $t--) {
                    if ($tokens[$t]['code'] !== \T_WHITESPACE) {
                        $prev = $t;
                        break;
                    }
                }

                if ($prev !== false && $tokens[$prev]['code'] === \T_USE) {
                    // Closure use by reference.
                    return true;
                }
            }
        }

        // Pass by reference in function calls and assign by reference in arrays.
        if ($tokens[$tokenBefore]['code'] === \T_OPEN_PARENTHESIS
            || $tokens[$tokenBefore]['code'] === \T_COMMA
            || $tokens[$tokenBefore]['code'] === \T_OPEN_SHORT_ARRAY
        ) {
            if ($tokens[$tokenAfter]['code'] === \T_VARIABLE) {
                return true;
            } else {
                $skip   = Tokens::$emptyTokens;
                $skip[] = \T_NS_SEPARATOR;
                $skip[] = \T_SELF;
                $skip[] = \T_PARENT;
                $skip[] = \T_STATIC;
                $skip[] = \T_STRING;
                $skip[] = \T_NAMESPACE;
                $skip[] = \T_DOUBLE_COLON;

                $nextSignificantAfter = $phpcsFile->findNext(
                    $skip,
                    ($stackPtr + 1),
                    null,
                    true
                );
                if ($tokens[$nextSignificantAfter]['code'] === \T_VARIABLE) {
                    return true;
                }
            }
        }

        return false;
    }
}
