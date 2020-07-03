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

use PHPCSUtils\BackCompat\Helper;

/**
 * Utility functions for checking the orthography of arbitrary text strings.
 *
 * > An orthography is a set of conventions for writing a language. It includes norms of spelling,
 * > hyphenation, capitalization, word breaks, emphasis, and punctuation.
 * > Source: https://en.wikipedia.org/wiki/Orthography
 *
 * @since 1.0.0
 */
class Orthography
{

    /**
     * Characters which are considered terminal points for a sentence.
     *
     * @link https://www.thepunctuationguide.com/terminal-points.html Punctuation guide on terminal points.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const TERMINAL_POINTS = '.?!';

    /**
     * Check if the first character of an arbitrary text string is a capital letter.
     *
     * Letter characters which do not have a concept of lower/uppercase will
     * be accepted as correctly capitalized.
     *
     * @since 1.0.0
     *
     * @param string $string The text string to examine.
     *                       This can be the contents of a text string token,
     *                       but also, for instance, a comment text.
     *                       Potential text delimiter quotes should be stripped
     *                       off a text string before passing it to this method.
     *                       Also see: {@see \PHPCSUtils\Utils\TextStrings::stripQuotes()}.
     *
     * @return bool `TRUE` when the first character is a capital letter or a letter
     *              which doesn't have a concept of capitalization.
     *              `FALSE` otherwise, including for non-letter characters.
     */
    public static function isFirstCharCapitalized($string)
    {
        $string = \ltrim($string);
        return (\preg_match('`^[\p{Lu}\p{Lt}\p{Lo}]`u', $string) > 0);
    }

    /**
     * Check if the first character of an arbitrary text string is a lowercase letter.
     *
     * @since 1.0.0
     *
     * @param string $string The text string to examine.
     *                       This can be the contents of a text string token,
     *                       but also, for instance, a comment text.
     *                       Potential text delimiter quotes should be stripped
     *                       off a text string before passing it to this method.
     *                       Also see: {@see \PHPCSUtils\Utils\TextStrings::stripQuotes()}.
     *
     * @return bool `TRUE` when the first character is a lowercase letter.
     *              `FALSE` otherwise, including for letters which don't have a concept of
     *              capitalization and for non-letter characters.
     */
    public static function isFirstCharLowercase($string)
    {
        $string = \ltrim($string);
        return (\preg_match('`^\p{Ll}`u', $string) > 0);
    }

    /**
     * Capitalize the first character of an arbitrary text string if it is a lowercase letter.
     *
     * Multibyte safe version of ucfirst(), which takes the file encoding, as set in a PHPCS ruleset,
     * into account.
     *
     * @since 1.0.0
     *
     * @param string $string The text string to transform.
     *                       This can be the contents of a text string token,
     *                       but also, for instance, a comment text.
     *                       Potential text delimiter quotes should be stripped
     *                       off a text string before passing it to this method.
     *                       Also see: {@see \PHPCSUtils\Utils\TextStrings::stripQuotes()}.
     *
     * @return string|false The transformed string or the unchanged string if the first character
     *                      was not a lowercase letter.
     *                      `FALSE` if a transformation is warranted, but not possible due to
     *                      the MBString extension not being available and the first character
     *                      being non-ascii or if the transformation would result in an
     *                      invalid text string for the current encoding.
     */
    public static function capitalizeFirstChar($string)
    {
        static $mbstring = null, $encoding = null;

        // Cache the results of MbString check and encoding. These values won't change during a run.
        if (isset($mbstring) === false) {
            $mbstring = \function_exists('mb_ereg_replace_callback');
        }

        if (isset($encoding) === false) {
            $encoding = Helper::getEncoding();
        }

        if (self::isFirstCharLowercase($string) === false) {
            // First character is not a lowercase letter, so attempting to change it won't have any effect anyway.
            return $string;
        }

        if ($mbstring === true) {
            \mb_regex_set_options('z');
            \mb_regex_encoding($encoding);

            // phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.mb_ereg_replace_callbackFound -- Available since PHP 5.4.1.
            $ucfirst = \mb_ereg_replace_callback(
                '^\s*\p{Ll}',
                function ($matches) use ($encoding) {
                    return \mb_strtoupper($matches[0], $encoding);
                },
                $string
            );

            // Make sure the output is actually a valid text string for the given encoding.
            if (\mb_check_encoding($ucfirst, $encoding) === true) {
                return $ucfirst;
            }
        }

        // MBString is not available. Try ucfirst() - should only change strings which the locale can handle.
        $ucfirst = \ucfirst($string);
        if ($string !== $ucfirst) {
            return $ucfirst;
        }

        return false;
    }

    /**
     * Check if the last character of an arbitrary text string is a valid punctuation character.
     *
     * @since 1.0.0
     *
     * @param string $string       The text string to examine.
     *                             This can be the contents of a text string token,
     *                             but also, for instance, a comment text.
     *                             Potential text delimiter quotes should be stripped
     *                             off a text string before passing it to this method.
     *                             Also see: {@see \PHPCSUtils\Utils\TextStrings::stripQuotes()}.
     * @param string $allowedChars Characters which are considered valid punctuation
     *                             to end the text string.
     *                             Defaults to `'.?!'`, i.e. a full stop, question mark
     *                             or exclamation mark.
     *
     * @return bool
     */
    public static function isLastCharPunctuation($string, $allowedChars = self::TERMINAL_POINTS)
    {
        static $encoding;

        if (isset($encoding) === false) {
            $encoding = Helper::getEncoding();
        }

        $string = \rtrim($string);
        if (\function_exists('iconv_substr') === true) {
            $lastChar = \iconv_substr($string, -1, 1, $encoding);
        } else {
            $lastChar = \substr($string, -1);
        }

        if (\function_exists('iconv_strpos') === true) {
            return (\iconv_strpos($allowedChars, $lastChar, 0, $encoding) !== false);
        } else {
            return (\strpos($allowedChars, $lastChar) !== false);
        }
    }
}
