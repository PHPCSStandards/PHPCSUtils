<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCS23Utils\Sniffs\Load;

/*
 * Here be magic.
 *
 * This `include` allows for the Utility functions to work in both PHPCS 2.x as well as PHPCS 3.x.
 */
require_once \dirname(\dirname(\dirname(__DIR__))) . '/phpcsutils-autoload.php';

/**
 * Dummy Sniff.
 *
 * This sniff doesn't do anything. It's just here to trigger the above include.
 *
 * @since 1.0.0
 */
class LoadUtilsSniff
{

    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [];
    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process($phpcsFile, $stackPtr)
    {
    }
}
