<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\AbstractSniffs\AbstractArrayDeclaration;

use PHP_CodeSniffer\Files\File;
use PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff;

/**
 * Test double for the AbstractArrayDeclarationSniff to allow for testing the getActualArrayKey() method.
 *
 * @since 1.0.0
 */
final class ArrayDeclarationSniffTestDouble extends AbstractArrayDeclarationSniff
{

    /**
     * The token stack for the current file being examined.
     *
     * @var array<int, array<string, mixed>>
     */
    public $tokens;

    /**
     * Process every part of the array declaration.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     *
     * @return void
     */
    public function processArray(File $phpcsFile)
    {
    }
}
