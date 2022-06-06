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
use PHPCSUtils\Tokens\Collections;

/**
 * Utility functions for working with text string tokens.
 *
 * @since 1.0.0
 */
class TextStrings
{

    /**
     * Get the complete contents of a - potentially multi-line - text string.
     *
     * PHPCS tokenizes multi-line text strings with a single token for each line.
     * This method can be used to retrieve the text string as it would be received and
     * processed in PHP itself.
     *
     * This method is particularly useful for sniffs which examine the contents of text strings,
     * where the content matching might result in false positives/false negatives if the text
     * were to be examined line by line.
     *
     * Additionally, this method correctly handles a particular type of double quoted string
     * with an embedded expression which is incorrectly tokenized in PHPCS itself prior to
     * PHPCS version 3.x.x.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile   The file where this token was found.
     * @param int                         $stackPtr    Pointer to the first text string token
     *                                                 of a - potentially multi-line - text string
     *                                                 or to a Nowdoc/Heredoc opener.
     * @param bool                        $stripQuotes Optional. Whether to strip text delimiter
     *                                                 quotes off the resulting text string.
     *                                                 Defaults to `true`.
     *
     * @return string The complete text string.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      valid text string token.
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not the _first_
     *                                                      token in a text string.
     */
    public static function getCompleteTextString(File $phpcsFile, $stackPtr, $stripQuotes = true)
    {
        $tokens = $phpcsFile->getTokens();

        // Must be the start of a text string token.
        if (isset($tokens[$stackPtr], Collections::textStringStartTokens()[$tokens[$stackPtr]['code']]) === false) {
            throw new RuntimeException(
                '$stackPtr must be of type T_START_HEREDOC, T_START_NOWDOC, T_CONSTANT_ENCAPSED_STRING'
                . ' or T_DOUBLE_QUOTED_STRING'
            );
        }

        if ($tokens[$stackPtr]['code'] === \T_CONSTANT_ENCAPSED_STRING
            || $tokens[$stackPtr]['code'] === \T_DOUBLE_QUOTED_STRING
        ) {
            $prev = $phpcsFile->findPrevious(\T_WHITESPACE, ($stackPtr - 1), null, true);
            if ($tokens[$stackPtr]['code'] === $tokens[$prev]['code']) {
                throw new RuntimeException('$stackPtr must be the start of the text string');
            }
        }

        $stripNewline = false;

        switch ($tokens[$stackPtr]['code']) {
            case \T_START_HEREDOC:
                $stripQuotes  = false;
                $stripNewline = true;
                $targetType   = \T_HEREDOC;
                $current      = ($stackPtr + 1);
                break;

            case \T_START_NOWDOC:
                $stripQuotes  = false;
                $stripNewline = true;
                $targetType   = \T_NOWDOC;
                $current      = ($stackPtr + 1);
                break;

            default:
                $targetType = $tokens[$stackPtr]['code'];
                $current    = $stackPtr;
                break;
        }

        $string = '';
        do {
            $string .= $tokens[$current]['content'];
            ++$current;
        } while (isset($tokens[$current]) && $tokens[$current]['code'] === $targetType);

        if ($targetType === \T_DOUBLE_QUOTED_STRING) {
            /*
             * BC for PHPCS < ??.
             * Prior to PHPCS 3.x.x, when a select group of embedded variables/expressions was encountered
             * in a double quoted string, the embed would not be tokenized as part of the T_DOUBLE_QUOTED_STRING,
             * but would still have the PHP native tokenization.
             */
            if (isset($tokens[$current]) && $tokens[$current]['code'] === \T_DOLLAR_OPEN_CURLY_BRACES) {
                $embeddedContent = $tokens[$current]['content'];
                $nestedVars      = [$current];
                $foundEnd        = false;

                for ($current = ($current + 1); $current < $phpcsFile->numTokens; $current++) {
                    if ($tokens[$current]['code'] === \T_DOUBLE_QUOTED_STRING
                        && empty($nestedVars) === true
                    ) {
                        $embeddedContent .= self::getCompleteTextString($phpcsFile, $current, false);
                        $foundEnd         = true;
                        break;
                    }

                    $embeddedContent .= $tokens[$current]['content'];

                    if (\strpos($tokens[$current]['content'], '{') !== false) {
                        $nestedVars[] = $current;
                    }

                    if (\strpos($tokens[$current]['content'], '}') !== false) {
                        \array_pop($nestedVars);
                    }
                }

                /*
                 * Only accept this as one of the broken tokenizations if this is not a parse error
                 * or if we reached the end of the file.
                 */
                if ($foundEnd === true || $current === $phpcsFile->numTokens) {
                    $string .= $embeddedContent;
                }
            }
        }

        if ($stripNewline === true) {
            // Heredoc/nowdoc: strip the new line at the end of the string to emulate how PHP sees the string.
            $string = \rtrim($string, "\r\n");
        }

        if ($stripQuotes === true) {
            return self::stripQuotes($string);
        }

        return $string;
    }

    /**
     * Strip text delimiter quotes from an arbitrary text string.
     *
     * Intended for use with the "contents" of a `T_CONSTANT_ENCAPSED_STRING` / `T_DOUBLE_QUOTED_STRING`.
     *
     * - Prevents stripping mis-matched quotes.
     * - Prevents stripping quotes from the textual content of the text string.
     *
     * @since 1.0.0
     *
     * @param string $textString The raw text string.
     *
     * @return string Text string without quotes around it.
     */
    public static function stripQuotes($textString)
    {
        return \preg_replace('`^([\'"])(.*)\1$`Ds', '$2', $textString);
    }
}
