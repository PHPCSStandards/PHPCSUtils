<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\AbstractSniffs;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Abstract sniff to easily examine all parts of an array declaration.
 *
 * @since 1.0.0
 */
abstract class AbstractArrayDeclarationSniff implements Sniff
{

    /**
     * The stack pointer to the array keyword or the short array open token.
     *
     * @since 1.0.0
     *
     * @var int
     */
    protected $stackPtr;

    /**
     * The token stack for the current file being examined.
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected $tokens;

    /**
     * The stack pointer to the array opener.
     *
     * @since 1.0.0
     *
     * @var int
     */
    protected $arrayOpener;

    /**
     * The stack pointer to the array closer.
     *
     * @since 1.0.0
     *
     * @var int
     */
    protected $arrayCloser;

    /**
     * A multi-dimentional array with information on each array item.
     *
     * The array index is 1-based and contains the following information on each array item:
     * - 'start' : The stack pointer to the first token in the array item.
     * - 'end'   : The stack pointer to the first token in the array item.
     * - 'raw'   : A string with the contents of all tokens between `start` and `end`.
     * - 'clean' : Same as `raw`, but all comment tokens have been stripped out.
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected $arrayItems;

    /**
     * How many items are in the array.
     *
     * @since 1.0.0
     *
     * @var int
     */
    protected $itemCount = 0;

    /**
     * Whether or not the array is single line.
     *
     * @since 1.0.0
     *
     * @var bool
     */
    protected $singleLine;

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @since 1.0.0
     *
     * @codeCoverageIgnore
     *
     * @return array
     */
    public function register()
    {
        return [
            \T_ARRAY,
            \T_OPEN_SHORT_ARRAY,
            \T_OPEN_SQUARE_BRACKET,
        ];
    }

    /**
     * Processes this test when one of its tokens is encountered.
     *
     * This method fills the properties with relevant information for examining the array
     * and then passes off to the `processArray()` method.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     * @param int                         $stackPtr  The position in the PHP_CodeSniffer
     *                                               file's token stack where the token
     *                                               was found.
     *
     * @return void
     */
    final public function process(File $phpcsFile, $stackPtr)
    {
        try {
            $this->arrayItems = PassedParameters::getParameters($phpcsFile, $stackPtr);
        } catch (RuntimeException $e) {
            // Parse error, short list, real square open bracket or incorrectly tokenized short array token.
            return;
        }

        $this->stackPtr    = $stackPtr;
        $this->tokens      = $phpcsFile->getTokens();
        $openClose         = Arrays::getOpenClose($phpcsFile, $stackPtr, true);
        $this->arrayOpener = $openClose['opener'];
        $this->arrayCloser = $openClose['closer'];
        $this->itemCount   = \count($this->arrayItems);

        $this->singleLine = true;
        if ($this->tokens[$openClose['opener']]['line'] !== $this->tokens[$openClose['closer']]['line']) {
            $this->singleLine = false;
        }

        $this->processArray($phpcsFile);

        // Reset select properties between calls to this sniff to lower memory usage.
        unset($this->tokens, $this->arrayItems);
    }

    /**
     * Process every part of the array declaration.
     *
     * This contains the default logic for the sniff, but can be overloaded in a concrete child class
     * if needed.
     *
     * @since 1.0.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     *
     * @return void
     */
    public function processArray(File $phpcsFile)
    {
        if ($this->processOpenClose($phpcsFile, $this->arrayOpener, $this->arrayCloser) === true) {
            return;
        }

        if ($this->itemCount === 0) {
            return;
        }

        foreach ($this->arrayItems as $itemNr => $arrayItem) {
            $arrowPtr = Arrays::getDoubleArrowPtr($phpcsFile, $arrayItem['start'], $arrayItem['end']);

            if ($arrowPtr !== false) {
                if ($this->processKey($phpcsFile, $arrayItem['start'], ($arrowPtr - 1), $itemNr) === true) {
                    return;
                }

                if ($this->processArrow($phpcsFile, $arrowPtr, $itemNr) === true) {
                    return;
                }

                if ($this->processValue($phpcsFile, ($arrowPtr + 1), $arrayItem['end'], $itemNr) === true) {
                    return;
                }
            } else {
                if ($this->processNoKey($phpcsFile, $arrayItem['start'], $itemNr) === true) {
                    return;
                }

                if ($this->processValue($phpcsFile, $arrayItem['start'], $arrayItem['end'], $itemNr) === true) {
                    return;
                }
            }

            $commaPtr = ($arrayItem['end'] + 1);
            if ($itemNr < $this->itemCount || $this->tokens[$commaPtr]['code'] === \T_COMMA) {
                if ($this->processComma($phpcsFile, $commaPtr, $itemNr) === true) {
                    return;
                }
            }
        }
    }

    /**
     * Process the array opener and closer.
     *
     * Optional method to be implemented in concrete child classes.
     *
     * @since 1.0.0
     *
     * @codeCoverageIgnore
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     * @param int                         $openPtr   The position of the array opener token in the token stack.
     * @param int                         $closePtr  The position of the array closer token in the token stack.
     *
     * @return true|void Returning `true` will short-circuit the sniff and stop processing.
     */
    public function processOpenClose(File $phpcsFile, $openPtr, $closePtr)
    {
    }

    /**
     * Process the tokens in an array key.
     *
     * Optional method to be implemented in concrete child classes.
     *
     * The $startPtr and $endPtr do not discount whitespace or comments, but are all inclusive to
     * allow examining all tokens in an array key.
     *
     * @since 1.0.0
     *
     * @codeCoverageIgnore
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     * @param int                         $startPtr  The stack pointer to the first token in the "key" part of
     *                                               an array item.
     * @param int                         $endPtr    The stack pointer to the last token in the "key" part of
     *                                               an array item.
     * @param int                         $itemNr    Which item in the array is being handled.
     *                                               1-based, i.e. the first item is item 1, the second 2 etc.
     *
     * @return true|void Returning `true` will short-circuit the array item loop and stop processing.
     */
    public function processKey(File $phpcsFile, $startPtr, $endPtr, $itemNr)
    {
    }

    /**
     * Process an array item without an array key.
     *
     * Optional method to be implemented in concrete child classes.
     *
     * @since 1.0.0
     *
     * @codeCoverageIgnore
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     * @param int                         $startPtr  The stack pointer to the first token in the array item,
     *                                               which in this case will be the first token of the array
     *                                               value part of the array item.
     * @param int                         $itemNr    Which item in the array is being handled.
     *                                               1-based, i.e. the first item is item 1, the second 2 etc.
     *
     * @return true|void Returning `true` will short-circuit the array item loop and stop processing.
     */
    public function processNoKey(File $phpcsFile, $startPtr, $itemNr)
    {
    }

    /**
     * Process the double arrow.
     *
     * Optional method to be implemented in concrete child classes.
     *
     * @since 1.0.0
     *
     * @codeCoverageIgnore
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     * @param int                         $arrowPtr  The stack pointer to the double arrow for the array item.
     * @param int                         $itemNr    Which item in the array is being handled.
     *                                               1-based, i.e. the first item is item 1, the second 2 etc.
     *
     * @return true|void Returning `true` will short-circuit the array item loop and stop processing.
     */
    public function processArrow(File $phpcsFile, $arrowPtr, $itemNr)
    {
    }

    /**
     * Process the tokens in an array value.
     *
     * Optional method to be implemented in concrete child classes.
     *
     * The $startPtr and $endPtr do not discount whitespace or comments, but are all inclusive to
     * allow examining all tokens in an array value.
     *
     * @since 1.0.0
     *
     * @codeCoverageIgnore
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     * @param int                         $startPtr  The stack pointer to the first token in the "value" part of
     *                                               an array item.
     * @param int                         $endPtr    The stack pointer to the last token in the "value" part of
     *                                               an array item.
     * @param int                         $itemNr    Which item in the array is being handled.
     *                                               1-based, i.e. the first item is item 1, the second 2 etc.
     *
     * @return true|void Returning `true` will short-circuit the array item loop and stop processing.
     */
    public function processValue(File $phpcsFile, $startPtr, $endPtr, $itemNr)
    {
    }

    /**
     * Process the comma after an array item.
     *
     * Optional method to be implemented in concrete child classes.
     *
     * @since 1.0.0
     *
     * @codeCoverageIgnore
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     * @param int                         $commaPtr  The stack pointer to the comma.
     * @param int                         $itemNr    Which item in the array is being handled.
     *                                               1-based, i.e. the first item is item 1, the second 2 etc.
     *
     * @return true|void Returning `true` will short-circuit the array item loop and stop processing.
     */
    public function processComma(File $phpcsFile, $commaPtr, $itemNr)
    {
    }
}
