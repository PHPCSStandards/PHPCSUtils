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
use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Internal\NoFileCache;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\GetTokensAsString;

/**
 * Utility functions for working with text string tokens.
 *
 * @since 1.0.0
 */
class TextStrings
{

    /**
     * Regex to match the start of an embedded variable/expression.
     *
     * Prevents matching escaped variables/expressions.
     *
     * @var string
     */
    const START_OF_EMBED = '`(?<!\\\\)(\\\\{2})*(\{\$|\$\{|\$(?=[a-zA-Z_\x7f-\xff]))`';

    /**
     * Regex to match a "type 1" - directly embedded - variable without the dollar sign.
     *
     * Allows for array access and property access in as far as supported (single level).
     *
     * @var string
     */
    const TYPE1_EMBED_AFTER_DOLLAR =
        '`(?P<varname>[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)(?:\??->(?P>varname)|\[[^\]\'"\s]+\])?`';

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
     * @return string The contents of the complete text string.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      valid text string token.
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not the _first_
     *                                                      token in a text string.
     */
    public static function getCompleteTextString(File $phpcsFile, $stackPtr, $stripQuotes = true)
    {
        $tokens = $phpcsFile->getTokens();
        $end    = self::getEndOfCompleteTextString($phpcsFile, $stackPtr);

        $stripNewline = false;
        if ($tokens[$stackPtr]['code'] === \T_START_HEREDOC || $tokens[$stackPtr]['code'] === \T_START_NOWDOC) {
            $stripQuotes  = false;
            $stripNewline = true;
            $stackPtr     = ($stackPtr + 1);
        }

        $contents = GetTokensAsString::normal($phpcsFile, $stackPtr, $end);

        if ($stripNewline === true) {
            // Heredoc/nowdoc: strip the new line at the end of the string to emulate how PHP sees the string.
            $contents = \rtrim($contents, "\r\n");
        }

        if ($stripQuotes === true) {
            return self::stripQuotes($contents);
        }

        return $contents;
    }

    /**
     * Get the stack pointer to the end of a - potentially multi-line - text string.
     *
     * This method also correctly handles a particular type of double quoted string
     * with an embedded expression which is incorrectly tokenized in PHPCS itself prior to
     * PHPCS version 3.7.0.
     *
     * @see \PHPCSUtils\Utils\TextStrings::getCompleteTextString() Retrieve the contents of a complete - potentially
     *                                                             multi-line - text string.
     *
     * @since 1.0.0-alpha4
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  Pointer to the first text string token
     *                                               of a - potentially multi-line - text string
     *                                               or to a Nowdoc/Heredoc opener.
     *
     * @return int Stack pointer to the last token in the text string.
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      valid text string token.
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not the _first_
     *                                                      token in a text string.
     */
    public static function getEndOfCompleteTextString(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Must be the start of a text string token.
        if (isset($tokens[$stackPtr], Collections::textStringStartTokens()[$tokens[$stackPtr]['code']]) === false) {
            throw new RuntimeException(
                '$stackPtr must be of type T_START_HEREDOC, T_START_NOWDOC, T_CONSTANT_ENCAPSED_STRING'
                . ' or T_DOUBLE_QUOTED_STRING'
            );
        }

        if (isset(Tokens::$stringTokens[$tokens[$stackPtr]['code']]) === true) {
            $prev = $phpcsFile->findPrevious(\T_WHITESPACE, ($stackPtr - 1), null, true);
            if ($tokens[$stackPtr]['code'] === $tokens[$prev]['code']) {
                throw new RuntimeException('$stackPtr must be the start of the text string');
            }
        }

        if (Cache::isCached($phpcsFile, __METHOD__, $stackPtr) === true) {
            return Cache::get($phpcsFile, __METHOD__, $stackPtr);
        }

        switch ($tokens[$stackPtr]['code']) {
            case \T_START_HEREDOC:
                $targetType = \T_HEREDOC;
                $current    = ($stackPtr + 1);
                break;

            case \T_START_NOWDOC:
                $targetType = \T_NOWDOC;
                $current    = ($stackPtr + 1);
                break;

            default:
                $targetType = $tokens[$stackPtr]['code'];
                $current    = $stackPtr;
                break;
        }

        while (isset($tokens[$current]) && $tokens[$current]['code'] === $targetType) {
            ++$current;
        }

        $lastPtr = ($current - 1);

        if ($targetType === \T_DOUBLE_QUOTED_STRING) {
            /*
             * BC for PHPCS < 3.7.0.
             * Prior to PHPCS 3.7.0, when a select group of embedded variables/expressions was encountered
             * in a double quoted string, the embed would not be tokenized as part of the T_DOUBLE_QUOTED_STRING,
             * but would still have the PHP native tokenization.
             */
            $end = self::getEndOfDoubleQuotedString($phpcsFile, $lastPtr);
            if ($end !== $lastPtr) {
                // Check if the double quoted string continues after the end of the embed.
                if ($tokens[$end]['code'] === \T_DOUBLE_QUOTED_STRING) {
                    // Handles `"Text {embed} Text` followed by new line and additional text.
                    $end = self::getEndOfCompleteTextString($phpcsFile, $end);
                } elseif ($tokens[($end + 1)]['code'] === \T_DOUBLE_QUOTED_STRING) {
                    // Handles `"Text {multi-line embed}` followed by additional text.
                    $end = self::getEndOfCompleteTextString($phpcsFile, ($end + 1));
                }
            }

            $lastPtr = $end;
        }

        Cache::set($phpcsFile, __METHOD__, $stackPtr, $lastPtr);
        return $lastPtr;
    }

    /**
     * Get the stack pointer to the end of a (single) double quoted text string.
     *
     * This method provides a protection layer against a tokenizer bug in PHPCS < 3.7.0.
     * When a select group of embedded variables/expressions was encountered in a double quoted string,
     * PHPCS would not tokenize the double quoted string as one token per line, but instead the original
     * PHP native tokens would be included in the token stream.
     * This throws off the scope map (which can't be helped), but also makes examining
     * the contents of double quoted strings difficult.
     *
     * This method can help remedy that.
     *
     * Note: If the embed is spread over multiple lines, the last token of the mistokenized
     * part of the embed will be returned. The double quoted string _may_ still continue after that.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/pull/3604 PHPCS PR #3604
     *
     * @see \PHPCSUtils\Utils\GetTokensAsString                         Methods to retrieve the contents of the
     *                                                                  combined tokens.
     * @see \PHPCSUtils\Utils\TextStrings::getEndOfCompleteTextString() Get the stack pointer to the end of a complete
     *                                                                  - potentially multi-line - text string.
     * @see \PHPCSUtils\Utils\TextStrings::getCompleteTextString()      Retrieve the contents of a complete -
     *                                                                  - potentially multi-line - text string.
     *
     * @since 1.0.0-alpha4
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file where this token was found.
     * @param int                         $stackPtr  Pointer to the first text string token
     *                                               of a - potentially multi-token - double
     *                                               quoted text string.
     *
     * @return int Stack pointer to the last token (which may be the original stack pointer).
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified position is not a
     *                                                      T_DOUBLE_QUOTED_STRING token.
     */
    public static function getEndOfDoubleQuotedString(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false || $tokens[$stackPtr]['code'] !== \T_DOUBLE_QUOTED_STRING) {
            throw new RuntimeException('$stackPtr must be of type T_DOUBLE_QUOTED_STRING');
        }

        $next = ($stackPtr + 1);
        if (isset($tokens[$next]) === false || $tokens[$next]['code'] !== \T_DOLLAR_OPEN_CURLY_BRACES) {
            // This code is not affected by the tokenizer bug.
            return $stackPtr;
        }

        /*
         * BC for PHPCS < 3.7.0.
         */
        $nestedBraces = [$next];
        for ($next = ($next + 1); $next < $phpcsFile->numTokens; $next++) {
            if ($tokens[$next]['code'] === \T_DOUBLE_QUOTED_STRING
                && empty($nestedBraces) === true
            ) {
                break;
            }

            if (\strpos($tokens[$next]['content'], '{') !== false) {
                $nestedBraces[] = $next;
            }

            if (\strpos($tokens[$next]['content'], '}') !== false) {
                \array_pop($nestedBraces);
            }
        }

        if ($next === $phpcsFile->numTokens) {
            return ($phpcsFile->numTokens - 1);
        }

        if ($tokens[$next]['line'] > $tokens[$stackPtr]['line']) {
            return ($next - 1);
        }

        return self::getEndOfDoubleQuotedString($phpcsFile, $next);
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

    /**
     * Get the embedded variables/expressions from an arbitrary string.
     *
     * Note: this function gets the complete variables/expressions _as they are embedded_,
     * i.e. including potential curly brace wrappers, array access, method calls etc.
     *
     * @param string $text The contents of a T_DOUBLE_QUOTED_STRING or T_HEREDOC token.
     *
     * @return array<int, string> Array of encountered variable names/expressions with the offset at which
     *                            the variable/expression was found in the string, as the key.
     */
    public static function getEmbeds($text)
    {
        return self::getStripEmbeds($text)['embeds'];
    }

    /**
     * Strip embedded variables/expressions from an arbitrary string.
     *
     * @param string $text The contents of a T_DOUBLE_QUOTED_STRING or T_HEREDOC token.
     *
     * @return string String without variables/expressions in it.
     */
    public static function stripEmbeds($text)
    {
        return self::getStripEmbeds($text)['remaining'];
    }

    /**
     * Split an arbitrary text string into embedded variables/expressions and remaining text.
     *
     * PHP contains four types of embedding syntaxes:
     * 1. Directly embedding variables ("$foo");
     * 2. Braces outside the variable ("{$foo}");
     * 3. Braces after the dollar sign ("${foo}");
     * 4. Variable variables ("${expr}", equivalent to (string) ${expr}).
     *
     * Type 3 and 4 are deprecated as of PHP 8.2 and will be removed in PHP 9.0.
     *
     * This method handles all types of embeds, including recognition of whether an embed is escaped or not.
     *
     * @link https://www.php.net/manual/en/language.types.string.php#language.types.string.parsing
     * @link https://wiki.php.net/rfc/deprecate_dollar_brace_string_interpolation
     *
     * @param string $text The contents of a T_DOUBLE_QUOTED_STRING or T_HEREDOC token.
     *
     * @return array<string, mixed> Array containing two values:
     *                              1. An array containing a string representation of each embed encountered.
     *                                 The keys in this array are the integer offset within the original string
     *                                 where the embed was found.
     *                              2. The textual contents, embeds stripped out of it.
     *                              The format of the array return value is:
     *                              ```php
     *                              array(
     *                                'embeds'    => array<int, string>,
     *                                'remaining' => string,
     *                              )
     *                              ```
     */
    public static function getStripEmbeds($text)
    {
        if (\strpos($text, '$') === false) {
            return [
                'embeds'    => [],
                'remaining' => $text,
            ];
        }

        $textHash = \md5($text);
        if (NoFileCache::isCached(__METHOD__, $textHash) === true) {
            return NoFileCache::get(__METHOD__, $textHash);
        }

        $offset    = 0;
        $strLen    = \strlen($text); // Use iconv ?
        $stripped  = '';
        $variables = [];

        while (\preg_match(self::START_OF_EMBED, $text, $matches, \PREG_OFFSET_CAPTURE, $offset) === 1) {
            $stripped .= \substr($text, $offset, ($matches[2][1] - $offset));

            $matchedExpr   = $matches[2][0];
            $matchedOffset = $matches[2][1];
            $braces        = \substr_count($matchedExpr, '{');
            $newOffset     = $matchedOffset + \strlen($matchedExpr);

            if ($braces === 0) {
                /*
                 * Type 1: simple variable embed.
                 * Regex will always return a match due to the look ahead in the above regex.
                 */
                \preg_match(self::TYPE1_EMBED_AFTER_DOLLAR, $text, $endMatch, 0, $newOffset);
                $matchedExpr              .= $endMatch[0];
                $variables[$matchedOffset] = $matchedExpr;
                $offset                    = $newOffset + \strlen($endMatch[0]);
                continue;
            }

            for (; $newOffset < $strLen; $newOffset++) {
                if ($text[$newOffset] === '{') {
                    ++$braces;
                    continue;
                }

                if ($text[$newOffset] === '}') {
                    --$braces;
                    if ($braces === 0) {
                        $matchedExpr               = \substr($text, $matchedOffset, (1 + $newOffset - $matchedOffset));
                        $variables[$matchedOffset] = $matchedExpr;
                        $offset                    = ($newOffset + 1);
                        break;
                    }
                }
            }
        }

        if ($offset < $strLen) {
            // Add the end of the string.
            $stripped .= \substr($text, $offset);
        }

        $returnValue = [
            'embeds'    => $variables,
            'remaining' => $stripped,
        ];

        NoFileCache::set(__METHOD__, $textHash, $returnValue);
        return $returnValue;
    }
}
