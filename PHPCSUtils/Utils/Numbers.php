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
use PHPCSUtils\BackCompat\Helper;

/**
 * Utility functions for working with integer/float tokens.
 *
 * PHP 7.4 introduced numeric literal separators which break number tokenization in older PHP versions.
 * PHPCS backfills this since PHPCS 3.5.3/4.
 *
 * In other words, if an external standard intends to support PHPCS < 3.5.4 and PHP < 7.4, working
 * with number tokens has suddenly become a challenge.
 *
 * The functions in this class have been put in place to ease that pain and it is
 * *strongly* recommended to always use these functions when sniffing for and examining the
 * contents of `T_LNUMBER` or `T_DNUMBER` tokens.
 *
 * @link https://www.php.net/migration74.new-features.php#migration74.new-features.core.numeric-literal-separator
 *       PHP Manual on numeric literal separators.
 *
 * @since 1.0.0
 */
class Numbers
{

    /**
     * Regex to determine whether the contents of an arbitrary string represents a decimal integer.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const REGEX_DECIMAL_INT = '`^(?:0|[1-9][0-9]*)$`D';

    /**
     * Regex to determine whether the contents of an arbitrary string represents an octal integer.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const REGEX_OCTAL_INT = '`^0[0-7]+$`D';

    /**
     * Regex to determine whether the contents of an arbitrary string represents a binary integer.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const REGEX_BINARY_INT = '`^0b[0-1]+$`iD';

    /**
     * Regex to determine whether the contents of an arbitrary string represents a hexidecimal integer.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const REGEX_HEX_INT = '`^0x[0-9A-F]+$`iD';

    /**
     * Regex to determine whether the contents of an arbitrary string represents a float.
     *
     * @link https://www.php.net/language.types.float PHP Manual on floats
     *
     * @since 1.0.0
     *
     * @var string
     */
    const REGEX_FLOAT = '`
        ^(?:
            (?:
                (?:
                    (?P<LNUM>[0-9]+)
                |
                    (?P<DNUM>([0-9]*\.(?P>LNUM)|(?P>LNUM)\.[0-9]*))
                )
                [e][+-]?(?P>LNUM)
            )
            |
            (?P>DNUM)
            |
            (?:0|[1-9][0-9]*)
        )$
        `ixD';

    /**
     * Regex to determine if a T_STRING following a T_[DL]NUMBER is part of a numeric literal sequence.
     *
     * Cross-version compatibility helper for PHP 7.4 numeric literals with underscore separators.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const REGEX_NUMLIT_STRING = '`^((?<![\.e])_[0-9][0-9e\.]*)+$`iD';

    /**
     * Regex to determine is a T_STRING following a T_[DL]NUMBER is part of a hexidecimal numeric literal sequence.
     *
     * Cross-version compatibility helper for PHP 7.4 numeric literals with underscore separators.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const REGEX_HEX_NUMLIT_STRING = '`^((?<!\.)_[0-9A-F]*)+$`iD';

    /**
     * PHPCS versions in which the backfill for PHP 7.4 numeric literal separators is broken.
     *
     * @since 1.0.0
     * @since 1.0.0-alpha2 Changed from a property to a class constant.
     *                     Changed from an array to a string.
     *
     * @var string
     */
    const UNSUPPORTED_PHPCS_VERSION = '3.5.3';

    /**
     * Valid tokens which could be part of a numeric literal sequence in PHP < 7.4.
     *
     * @since 1.0.0
     *
     * @var array
     */
    private static $numericLiteralAcceptedTokens = [
        \T_LNUMBER => true,
        \T_DNUMBER => true,
        \T_STRING  => true,
    ];

    /**
     * Retrieve information about a number token in a cross-version compatible manner.
     *
     * Helper function to deal with numeric literals, potentially with underscore separators.
     *
     * PHP < 7.4 does not tokenize numeric literals containing underscores correctly.
     * As of PHPCS 3.5.3, PHPCS contains a backfill, but this backfill was buggy in the initial
     * implementation. A fix for this broken backfill is included in PHPCS 3.5.4.
     *
     * Either way, this function can be used with all PHPCS/PHP combinations and will, if necessary,
     * provide a backfill for PHPCS/PHP combinations where PHP 7.4 numbers with underscore separators
     * are tokenized incorrectly - with the exception of PHPCS 3.5.3 as the buggyness of the original
     * backfill implementation makes it impossible to provide reliable results.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/2546 PHPCS issue #2546
     * @link https://github.com/squizlabs/PHP_CodeSniffer/pull/2771   PHPCS PR #2771
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of a T_LNUMBER or T_DNUMBER token.
     *
     * @return array An array with information about the number.
     *               The format of the array return value is:
     *               ```php
     *               array(
     *                 'orig_content' => string, // The (potentially concatenated) original
     *                                           // content of the tokens;
     *                 'content'      => string, // The (potentially concatenated) content,
     *                                           // underscore(s) removed;
     *                 'code'         => int,    // The token code of the number, either
     *                                           // T_LNUMBER or T_DNUMBER.
     *                 'type'         => string, // The token type, either 'T_LNUMBER'
     *                                           // or 'T_DNUMBER'.
     *                 'decimal'      => string, // The decimal value of the number;
     *                 'last_token'   => int,    // The stackPtr to the last token which was
     *                                           // part of the number.
     *                                           // This will be the same as the original
     *                                           // stackPtr if it is not a PHP 7.4 number
     *                                           // with underscores.
     *               )
     *               ```
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the specified token is not of type
     *                                                      `T_LNUMBER` or `T_DNUMBER`.
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If this function is called in combination
     *                                                      with an unsupported PHPCS version.
     */
    public static function getCompleteNumber(File $phpcsFile, $stackPtr)
    {
        static $php74, $phpcsVersion, $phpcsWithBackfill;

        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]) === false
            || ($tokens[$stackPtr]['code'] !== \T_LNUMBER && $tokens[$stackPtr]['code'] !== \T_DNUMBER)
        ) {
            throw new RuntimeException(
                'Token type "' . $tokens[$stackPtr]['type'] . '" is not T_LNUMBER or T_DNUMBER'
            );
        }

        if (isset($php74, $phpcsVersion, $phpcsWithBackfill) === false) {
            $php74             = \version_compare(\PHP_VERSION_ID, '70399', '>');
            $phpcsVersion      = Helper::getVersion();
            $phpcsWithBackfill = \version_compare($phpcsVersion, self::UNSUPPORTED_PHPCS_VERSION, '>');
        }

        /*
         * Bow out for PHPCS version(s) with broken tokenization of PHP 7.4 numeric literals with
         * separators, including for PHP 7.4, as the backfill kicks in for PHP 7.4 while it shouldn't.
         *
         * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/2546
         */
        if (\version_compare($phpcsVersion, self::UNSUPPORTED_PHPCS_VERSION, '==') === true) {
            throw new RuntimeException('The ' . __METHOD__ . '() method does not support PHPCS ' . $phpcsVersion);
        }

        $content = $tokens[$stackPtr]['content'];
        $result  = [
            'orig_content' => $content,
            'content'      => \str_replace('_', '', $content),
            'code'         => $tokens[$stackPtr]['code'],
            'type'         => $tokens[$stackPtr]['type'],
            'decimal'      => self::getDecimalValue($content),
            'last_token'   => $stackPtr,
        ];

        // When things are already correctly tokenized, there's not much to do.
        if ($php74 === true
            || $phpcsWithBackfill === true
            || isset($tokens[($stackPtr + 1)]) === false
            || $tokens[($stackPtr + 1)]['code'] !== \T_STRING
            || $tokens[($stackPtr + 1)]['content'][0] !== '_'
        ) {
            return $result;
        }

        $hex = false;
        if (\strpos($content, '0x') === 0) {
            $hex = true;
        }

        $lastChar = \substr($content, -1);
        if (\preg_match('`[0-9]`', $lastChar) !== 1) {
            if ($hex === false || \preg_match('`[A-F]`i', $lastChar) !== 1) {
                // Last character not valid for numeric literal sequence with underscores.
                // No need to look any further.
                return $result;
            }
        }

        /*
         * OK, so this could potentially be a PHP 7.4 number with an underscore separator with PHPCS
         * being run on PHP < 7.4.
         */

        $regex = self::REGEX_NUMLIT_STRING;
        if ($hex === true) {
            $regex = self::REGEX_HEX_NUMLIT_STRING;
        }

        $next      = $stackPtr;
        $lastToken = $stackPtr;

        while (isset($tokens[++$next], self::$numericLiteralAcceptedTokens[$tokens[$next]['code']]) === true) {
            if ($tokens[$next]['code'] === \T_STRING
                && \preg_match($regex, $tokens[$next]['content']) !== 1
            ) {
                break;
            }

            $content  .= $tokens[$next]['content'];
            $lastToken = $next;
            $lastChar  = \substr(\strtolower($content), -1);

            // Support floats.
            if ($lastChar === 'e'
                && isset($tokens[($next + 1)], $tokens[($next + 2)]) === true
                && ($tokens[($next + 1)]['code'] === \T_MINUS
                || $tokens[($next + 1)]['code'] === \T_PLUS)
                && $tokens[($next + 2)]['code'] === \T_LNUMBER
            ) {
                $content  .= $tokens[($next + 1)]['content'];
                $content  .= $tokens[($next + 2)]['content'];
                $next     += 2;
                $lastToken = $next;
            }

            // Don't look any further if the last char is not valid before a separator.
            if (\preg_match('`[0-9]`', $lastChar) !== 1) {
                if ($hex === false || \preg_match('`[a-f]`i', $lastChar) !== 1) {
                    break;
                }
            }
        }

        // OK, so we now have `content` including potential underscores. Let's strip them out.
        $result['orig_content'] = $content;
        $result['content']      = \str_replace('_', '', $content);
        $result['decimal']      = self::getDecimalValue($result['content']);
        $result['last_token']   = $lastToken;

        // Determine actual token type.
        $type = $result['type'];
        if ($type === 'T_LNUMBER') {
            if ($hex === false
                && (\strpos($result['content'], '.') !== false
                || \stripos($result['content'], 'e') !== false)
            ) {
                $type = 'T_DNUMBER';
            } elseif (($result['decimal'] + 0) > \PHP_INT_MAX) {
                $type = 'T_DNUMBER';
            }
        }

        $result['code'] = \constant($type);
        $result['type'] = $type;

        return $result;
    }

    /**
     * Get the decimal number value of a numeric string.
     *
     * Takes PHP 7.4 numeric literal separators in numbers into account.
     *
     * @since 1.0.0
     *
     * @param string $textString Arbitrary text string.
     *                           This text string should be the (combined) token content of
     *                           one or more tokens which together represent a number in PHP.
     *
     * @return string|false Decimal number as a string or `FALSE` if the passed parameter
     *                      was not a numeric string.
     *                      > Note: floating point numbers with exponent will not be expanded,
     *                      but returned as-is.
     */
    public static function getDecimalValue($textString)
    {
        if (\is_string($textString) === false || $textString === '') {
            return false;
        }

        /*
         * Remove potential PHP 7.4 numeric literal separators.
         *
         * {@internal While the is..() functions also do this, this is still needed
         * here to allow the hexdec(), bindec() functions to work correctly and for
         * the decimal/float to return a cross-version compatible decimal value.}
         */
        $textString = \str_replace('_', '', $textString);

        if (self::isDecimalInt($textString) === true) {
            return $textString;
        }

        if (self::isHexidecimalInt($textString) === true) {
            return (string) \hexdec($textString);
        }

        if (self::isBinaryInt($textString) === true) {
            return (string) \bindec($textString);
        }

        if (self::isOctalInt($textString) === true) {
            return (string) \octdec($textString);
        }

        if (self::isFloat($textString) === true) {
            return $textString;
        }

        return false;
    }

    /**
     * Verify whether the contents of an arbitrary string represents a decimal integer.
     *
     * Takes PHP 7.4 numeric literal separators in numbers into account.
     *
     * @since 1.0.0
     *
     * @param string $textString Arbitrary string.
     *
     * @return bool
     */
    public static function isDecimalInt($textString)
    {
        if (\is_string($textString) === false || $textString === '') {
            return false;
        }

        // Remove potential PHP 7.4 numeric literal separators.
        $textString = \str_replace('_', '', $textString);

        return (\preg_match(self::REGEX_DECIMAL_INT, $textString) === 1);
    }

    /**
     * Verify whether the contents of an arbitrary string represents a hexidecimal integer.
     *
     * Takes PHP 7.4 numeric literal separators in numbers into account.
     *
     * @since 1.0.0
     *
     * @param string $textString Arbitrary string.
     *
     * @return bool
     */
    public static function isHexidecimalInt($textString)
    {
        if (\is_string($textString) === false || $textString === '') {
            return false;
        }

        // Remove potential PHP 7.4 numeric literal separators.
        $textString = \str_replace('_', '', $textString);

        return (\preg_match(self::REGEX_HEX_INT, $textString) === 1);
    }

    /**
     * Verify whether the contents of an arbitrary string represents a binary integer.
     *
     * Takes PHP 7.4 numeric literal separators in numbers into account.
     *
     * @since 1.0.0
     *
     * @param string $textString Arbitrary string.
     *
     * @return bool
     */
    public static function isBinaryInt($textString)
    {
        if (\is_string($textString) === false || $textString === '') {
            return false;
        }

        // Remove potential PHP 7.4 numeric literal separators.
        $textString = \str_replace('_', '', $textString);

        return (\preg_match(self::REGEX_BINARY_INT, $textString) === 1);
    }

    /**
     * Verify whether the contents of an arbitrary string represents an octal integer.
     *
     * Takes PHP 7.4 numeric literal separators in numbers into account.
     *
     * @since 1.0.0
     *
     * @param string $textString Arbitrary string.
     *
     * @return bool
     */
    public static function isOctalInt($textString)
    {
        if (\is_string($textString) === false || $textString === '') {
            return false;
        }

        // Remove potential PHP 7.4 numeric literal separators.
        $textString = \str_replace('_', '', $textString);

        return (\preg_match(self::REGEX_OCTAL_INT, $textString) === 1);
    }

    /**
     * Verify whether the contents of an arbitrary string represents a floating point number.
     *
     * Takes PHP 7.4 numeric literal separators in numbers into account.
     *
     * @since 1.0.0
     *
     * @param string $textString Arbitrary string.
     *
     * @return bool
     */
    public static function isFloat($textString)
    {
        if (\is_string($textString) === false || $textString === '') {
            return false;
        }

        // Remove potential PHP 7.4 numeric literal separators.
        $textString = \str_replace('_', '', $textString);

        return (\preg_match(self::REGEX_FLOAT, $textString) === 1);
    }
}
