<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\ContextTracking;

use PHP_CodeSniffer\Files\File;

/**
 * Tracker interface.
 *
 * Aside from the methods declared here, each tracker will also contain a "getContext"-like method,
 * but this method may take different arguments and have a different type of return value
 * depending on what's being tracked, so that method is not defined in this interface.
 *
 * @since 1.1.0
 */
interface Tracker
{

    /**
     * Get the tracker instance.
     *
     * Trackers should always be singletons to get optimal performance benefits from using the tracker.
     * By using singletons, any token walking done in the tracker will only need to be done once
     * and the results can be re-used by all sniff using the tracker.
     *
     * @since 1.1.0
     *
     * @return \PHPCSUtils\ContextTracking\Tracker
     */
    public static function getInstance();

    /**
     * Reset all relevant properties to their default value.
     *
     * This method is intended for internal use in a Tracker class only!
     *
     * Typically used to clear the tracked data when a new file is seen.
     *
     * @since 1.1.0
     *
     * @return void
     */
    public function reset();

    /**
     * Retrieve the token constants this tracker needs a sniff to listen for to allow it to function.
     *
     * This method should be called from the `register()` method of any sniff using the tracker and the return value
     * should be included in the return value for the sniff `register()` method.
     *
     * @since 1.1.0
     *
     * @return array<int|string, int|string> An array with the token constants as both the key as well as the value.
     */
    public function getTargetTokens();

    /**
     * Keep track of a certain context.
     *
     * This method should fill the class properties of the tracker class with relevant information
     * about the context.
     *
     * This method should be called at the start of the `process()` method for any sniff using the tracker.
     *
     * This method should be highly optimized for performance and should silently ignore any invalid input received.
     *
     * @since 1.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the
     *                                               token was found.
     * @param int                         $stackPtr  The position in the PHP_CodeSniffer
     *                                               file's token stack where the token
     *                                               was found.
     *
     * @return void
     */
    public function track(File $phpcsFile, $stackPtr);
}
