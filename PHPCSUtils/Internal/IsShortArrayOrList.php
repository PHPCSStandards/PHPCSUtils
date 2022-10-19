<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Internal;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Lists;
use PHPCSUtils\Utils\Parentheses;

/**
 * Determination of short array vs short list vs square brackets.
 *
 * ---------------------------------------------------------------------------------------------
 * This class is only intended for internal use by PHPCSUtils and is not part of the public API.
 * This also means that it has no promise of backward compatibility.
 *
 * End-users should use the {@see \PHPCSUtils\Utils\Arrays::isShortArray()}
 * or the {@see \PHPCSUtils\Utils\Lists::isShortList()} method instead.
 * ---------------------------------------------------------------------------------------------
 *
 * @internal
 *
 * @since 1.0.0-alpha4
 */
final class IsShortArrayOrList
{

    /**
     * Type annotation for short arrays.
     *
     * @since 1.0.0-alpha4
     *
     * @var string
     */
    const SHORT_ARRAY = 'short array';

    /**
     * Type annotation for short lists.
     *
     * @since 1.0.0-alpha4
     *
     * @var string
     */
    const SHORT_LIST = 'short list';

    /**
     * Type annotation for square brackets.
     *
     * @since 1.0.0-alpha4
     *
     * @var string
     */
    const SQUARE_BRACKETS = 'square brackets';

    /**
     * The PHPCS file in which the current stackPtr was found.
     *
     * @since 1.0.0-alpha4
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    private $phpcsFile;

    /**
     * The current stack pointer.
     *
     * @since 1.0.0-alpha4
     *
     * @var int
     */
    private $stackPtr;

    /**
     * The token stack from the current file.
     *
     * @since 1.0.0-alpha4
     *
     * @var array
     */
    private $tokens;

    /**
     * Stack pointer to the open bracket.
     *
     * @since 1.0.0-alpha4
     *
     * @var int
     */
    private $opener;

    /**
     * Stack pointer to the close bracket.
     *
     * @since 1.0.0-alpha4
     *
     * @var int
     */
    private $closer;

    /**
     * Stack pointer to the first non-empty token before the open bracket.
     *
     * @since 1.0.0-alpha4
     *
     * @var int
     */
    private $beforeOpener;

    /**
     * Stack pointer to the first non-empty token after the close bracket.
     *
     * @since 1.0.0-alpha4
     *
     * @var int|false Will be `false` if the close bracket is the last token in the file.
     */
    private $afterCloser;

    /**
     * Current PHPCS version being used.
     *
     * @since 1.0.0-alpha4
     *
     * @var string
     */
    private $phpcsVersion;

    /**
     * Constructor.
     *
     * @since 1.0.0-alpha4
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the short array bracket token.
     *
     * @return void
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException If the token passed is not one of the
     *                                                      accepted types or doesn't exist.
     */
    public function __construct(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (isset($tokens[$stackPtr]) === false
            || isset(Collections::shortArrayTokensBC()[$tokens[$stackPtr]['code']]) === false
        ) {
            throw new RuntimeException(
                'The IsShortArrayOrList class expects to be passed a short array or square bracket token.'
            );
        }

        $this->phpcsFile = $phpcsFile;
        $this->stackPtr  = $stackPtr;
        $this->tokens    = $tokens;

        $this->opener = $stackPtr;
        if (isset($this->tokens[$stackPtr]['bracket_opener'])) {
            $this->opener = $this->tokens[$stackPtr]['bracket_opener'];
        }

        $this->closer = $stackPtr;
        if (isset($this->tokens[$stackPtr]['bracket_closer'])) {
            $this->closer = $this->tokens[$stackPtr]['bracket_closer'];
        }

        $this->beforeOpener = $this->phpcsFile->findPrevious(Tokens::$emptyTokens, ($this->opener - 1), null, true);
        $this->afterCloser  = $this->phpcsFile->findNext(Tokens::$emptyTokens, ($this->closer + 1), null, true);

        $this->phpcsVersion = Helper::getVersion();
    }

    /**
     * Determine whether the bracket is a short array, short list or real square bracket.
     *
     * @since 1.0.0-alpha4
     *
     * @return string Either 'short array', 'short list' or 'square brackets'.
     */
    public function solve()
    {
        if ($this->isSquareBracket() === true) {
            return self::SQUARE_BRACKETS;
        }

        // If the array closer is followed by an equals sign, it's always a short list.
        if ($this->afterCloser !== false && $this->tokens[$this->afterCloser]['code'] === \T_EQUAL) {
            return self::SHORT_LIST;
        }

        $type = $this->isInForeach();
        if ($type !== false) {
            return $type;
        }

        // Maybe this is a short list syntax nested inside another short list syntax ?
        $parentOpen = $this->opener;
        do {
            $parentOpen = $this->phpcsFile->findPrevious(
                \T_OPEN_SHORT_ARRAY,
                ($parentOpen - 1),
                null,
                false,
                null,
                true
            );

            if ($parentOpen === false) {
                return self::SHORT_ARRAY;
            }
        } while (isset($this->tokens[$parentOpen]['bracket_closer']) === true
            && $this->tokens[$parentOpen]['bracket_closer'] < $this->opener
        );

        $recursedReturn = Lists::isShortList($this->phpcsFile, $parentOpen);
        if ($recursedReturn === true) {
            return self::SHORT_LIST;
        }

        return self::SHORT_ARRAY;
    }

    /**
     * Check if the brackets are in actual fact real square brackets.
     *
     * @since 1.0.0-alpha4
     *
     * @return bool TRUE if these are real square brackets; FALSE otherwise.
     */
    private function isSquareBracket()
    {
        if ($this->opener === $this->closer) {
            // Parse error (unclosed bracket) or live coding. Bow out.
            return true;
        }

        // Check if this is a bracket we need to examine or a mistokenization.
        return ($this->isShortArrayBracket() === false);
    }

    /**
     * Verify that the current set of brackets is not affected by known PHPCS cross-version tokenizer issues.
     *
     * List of current tokenizer issues which affect the short array/short list tokenization:
     * - {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/3632 PHPCS#3632} (PHPCS < 3.7.2)
     *
     * List of previous tokenizer issues which affected the short array/short list tokenization for reference:
     * - {@link https://github.com/squizlabs/PHP_CodeSniffer/issues/1284 PHPCS#1284} (PHPCS < 2.8.1)
     * - {@link https://github.com/squizlabs/PHP_CodeSniffer/issues/1381 PHPCS#1381} (PHPCS < 2.9.0)
     * - {@link https://github.com/squizlabs/PHP_CodeSniffer/issues/1971 PHPCS#1971} (PHPCS 2.8.0 - 3.2.3)
     * - {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/3013 PHPCS#3013} (PHPCS < 3.5.6)
     * - {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/3172 PHPCS#3172} (PHPCS < 3.6.0)
     *
     * @since 1.0.0-alpha4
     *
     * @return bool TRUE if this is actually a short array bracket which needs to be examined,
     *              FALSE if it is an (incorrectly tokenized) square bracket.
     */
    private function isShortArrayBracket()
    {
        if ($this->tokens[$this->opener]['code'] === \T_OPEN_SQUARE_BRACKET) {
            if (\version_compare($this->phpcsVersion, '3.7.2', '>=') === true) {
                // These will just be properly tokenized, plain square brackets. No need for further checks.
                return false;
            }

            /*
             * BC: Work around a bug in the tokenizer of PHPCS < 3.7.2, where a `[` would be
             * tokenized as T_OPEN_SQUARE_BRACKET instead of T_OPEN_SHORT_ARRAY if it was
             * preceded by the close parenthesis of a non-braced control structure.
             *
             * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/3632
             */
            if ($this->tokens[$this->beforeOpener]['code'] === \T_CLOSE_PARENTHESIS
                && isset($this->tokens[$this->beforeOpener]['parenthesis_owner']) === true
                // phpcs:ignore Generic.Files.LineLength.TooLong
                && isset(Tokens::$scopeOpeners[$this->tokens[$this->tokens[$this->beforeOpener]['parenthesis_owner']]['code']]) === true
            ) {
                return true;
            }

            // These are really just plain square brackets.
            return false;
        }

        return true;
    }

    /**
     * Check is this set of brackets is used within a foreach expression.
     *
     * @since 1.0.0-alpha4
     *
     * @return string|false The determined type or FALSE if undetermined.
     */
    private function isInForeach()
    {
        if ($this->beforeOpener !== false
            && ($this->tokens[$this->beforeOpener]['code'] === \T_AS
                || $this->tokens[$this->beforeOpener]['code'] === \T_DOUBLE_ARROW)
            && Parentheses::lastOwnerIn($this->phpcsFile, $this->beforeOpener, \T_FOREACH) !== false
        ) {
            return self::SHORT_LIST;
        }

        return false;
    }
}
