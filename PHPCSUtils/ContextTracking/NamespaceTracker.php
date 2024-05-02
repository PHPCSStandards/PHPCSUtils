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
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\ContextTracking\Tracker;
use PHPCSUtils\Exceptions\OutOfBoundsStackPtr;
use PHPCSUtils\Exceptions\TypeError;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Conditions;
use PHPCSUtils\Utils\Namespaces;

/**
 * Namespace tracker.
 *
 * This tracks the namespace(s) seen in a file and allows a sniff to retrieve the
 * applicable namespace at any point in the current (last seen for tracking) file.
 *
 * Example code for implementing the tracker:
 * ```php
 * <?php
 * namespace YourNamespace\Sniffs\Category;
 *
 * use PHPCSUtils\ContextTracking\NamespaceTracker;
 *
 * class MySniff implements Sniff
 * {
 *     private $nsContext;
 *
 *     public function __construct()
 *     {
 *         $this->nsContext = NamespaceTracker::getInstance();
 *     }
 *
 *     public function register()
 *     {
 *         $targets = $this->nsContext->getTargetTokens();
 *
 *         // Add the tokens your own sniff targets.
 *         $targets[] = T_...
 *
 *         return $targets;
 *     }
 *
 *     public function process(File $phpcsFile, $stackPtr)
 *     {
 *         // Note: this call has to be at the top of the process method.
 *         // For best performance, this method should be called UNconditionally.
 *         $this->nsContext->track($phpcsFile, $stackPtr);
 *
 *         // Do your own processing.
 *
 *         // You can now use the `NamespaceTracker::getNamespace()` method to get access to the applicable
 *         // namespace for any token within the current file.
 *         // Note: the tracker has no opinion on whether the token is _subject to_ namespacing !
 *
 *         if ($this->nsContext->getNamespace($phpcsFile, $stackPtr) === '') {
 *             // Do something relevant for code in the global namespace.
 *         } else {
 *             // Do something relevant for namespaced code.
 *         }
 *     }
 * }
 * ```
 *
 * @since 1.1.0
 */
final class NamespaceTracker implements Tracker
{

    /**
     * File name for the file currently being examined.
     *
     * @since 1.1.0
     *
     * @var string
     */
    private $currentFile;

    /**
     * Last seen stack pointer in the current file.
     *
     * @since 1.1.0
     *
     * @var int
     */
    private $lastSeenPtr;

    /**
     * The last index in the $seenInFile array.
     *
     * @since 1.1.0
     *
     * @var int
     */
    private $currentNamespacePtr;

    /**
     * Keep track of all namespaces seen in this file.
     *
     * @since 1.1.0
     *
     * @var array<int, array<string, int|string|null>> Key is a numeric index.
     *                                                 Value is an array with the following keys:
     *                                                 - 'start' int      Stack pointer to the effective start of
     *                                                                    the namespace.
     *                                                 - 'end'   int|null Stack pointer to the end of the namespace or
     *                                                                    NULL if the end of the namespace is unknown
     *                                                                    (unscoped namespace).
     *                                                 - 'name'  string   Namespace name.
     */
    private $seenInFile;

    /**
     * Default value for the $seenInFile property when the file has not been scanned yet.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @since 1.1.0
     *
     * @var array<int, array<string, int|string|null>>
     */
    private $seenInFileDefault = [
        0 => [
            'start' => 0,
            'end'   => null,
            'name'  => '',
        ],
    ];

    /**
     * Tokens to track when backfilling a file.
     *
     * This token array has been set up for optimal performance, by skipping as much as possible.
     *
     * @since 1.1.0
     *
     * @var array<int|string, int|string>
     */
    private $backfillTrackTargets;

    /**
     * Singleton instance of this tracker.
     *
     * @since 1.1.0
     *
     * @var \PHPCSUtils\ContextTracking\NamespaceTracker
     */
    private static $instance;

    /**
     * Get the tracker instance.
     *
     * @since 1.1.0
     *
     * @return \PHPCSUtils\ContextTracking\NamespaceTracker
     */
    public static function getInstance()
    {
        if (isset(self::$instance) === false) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Enforce singleton.
     *
     * @since 1.1.0
     */
    private function __construct()
    {
        // Initialize the properties.
        $this->reset();

        // Set up the token array for the tokens to track when backfilling a file.
        $targets  = $this->getTargetTokens();
        $targets += Tokens::$scopeOpeners;
        $targets += Tokens::$parenthesisOpeners;
        $targets += [
            \T_OPEN_SHORT_ARRAY     => \T_OPEN_SHORT_ARRAY,
            \T_DOC_COMMENT_OPEN_TAG => \T_DOC_COMMENT_OPEN_TAG,
            \T_ATTRIBUTE            => \T_ATTRIBUTE,
        ];

        $this->backfillTrackTargets = $targets;
    }

    /**
     * Reset all relevant properties to their default value.
     *
     * @since 1.1.0
     *
     * @return void
     */
    public function reset()
    {
        $this->currentFile         = '';
        $this->lastSeenPtr         = -1;
        $this->currentNamespacePtr = 0;
        $this->seenInFile          = $this->seenInFileDefault;
    }

    /**
     * Retrieve the token constants this tracker needs a sniff to listen for to allow it to function.
     *
     * @since 1.1.0
     *
     * @return array<int|string, int|string>
     */
    public function getTargetTokens()
    {
        return [
            \T_NAMESPACE => \T_NAMESPACE,
        ];
    }

    /**
     * Track the namespace context.
     *
     * @since 1.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int                         $stackPtr  The current position in the PHP_CodeSniffer file.
     *
     * @return void
     */
    public function track(File $phpcsFile, $stackPtr)
    {
        /*
         * On the first token received from a new file, reset the properties.
         */
        $fileName = $phpcsFile->getFilename();
        if ($fileName !== $this->currentFile) {
            $this->reset();
            $this->currentFile = $fileName;
        }

        $tokens = $phpcsFile->getTokens();

        if (\is_int($stackPtr) === false || isset($tokens[$stackPtr]) === false) {
            // Invalid stack pointer, nothing to track. Ignore.
            return;
        }

        if ($this->lastSeenPtr >= $stackPtr) {
            /*
             * Don't do anything if this token has been handled already.
             * This is possible:
             * - when we are in a scoped namespace;
             * - when we've previously skipped to the end of something, like a closed scope;
             * - when a sniff has already called the `getNamespace[Info]()` method for a later token;
             * - or when the tracker has been injected into another tracker.
             */
            return;
        }

        // Record the last seen stackPtr.
        $this->lastSeenPtr = $stackPtr;

        /*
         * No need to look any further unless this is a namespace token.
         *
         * On the off-change that a function/class/array etc token has been passed,
         * check for a closer, as a namespace declaration can never be nested,
         * so we can ignore nested tokens when passed to the tracker.
         */
        if ($tokens[$stackPtr]['code'] !== \T_NAMESPACE) {
            // Skip past closures, anonymous classes and anything else scope related.
            if (isset($tokens[$stackPtr]['scope_closer'])) {
                if (isset($tokens[$stackPtr]['scope_condition']) === false
                    || $tokens[$tokens[$stackPtr]['scope_condition']]['code'] !== \T_NAMESPACE
                ) {
                    $this->lastSeenPtr = $tokens[$stackPtr]['scope_closer'];
                }
                return;
            }

            // Ignore anything within square brackets.
            if (isset($tokens[$stackPtr]['bracket_closer'])) {
                $this->lastSeenPtr = $tokens[$stackPtr]['bracket_closer'];
                return;
            }

            // Skip past nested arrays, function calls and arbitrary groupings.
            if (isset($tokens[$stackPtr]['parenthesis_closer'])) {
                $this->lastSeenPtr = $tokens[$stackPtr]['parenthesis_closer'];
                return;
            }

            // Skip over/out of potentially large docblocks.
            if (isset($tokens[$stackPtr]['comment_closer'])) {
                $this->lastSeenPtr = $tokens[$stackPtr]['comment_closer'];
                return;
            }

            // Skip over/out of attributes.
            if (isset($tokens[$stackPtr]['attribute_closer'])) {
                $this->lastSeenPtr = $tokens[$stackPtr]['attribute_closer'];
                return;
            }

            return;
        }

        $name = Namespaces::getDeclaredName($phpcsFile, $stackPtr);
        if ($name === false) {
            // Namespace operator or live coding/parse error.
            return;
        }

        /*
         * Handle a namespace declaration token.
         */
        if (isset($tokens[$stackPtr]['scope_opener'])) {
            $start             = ($tokens[$stackPtr]['scope_opener'] + 1);
            $end               = $tokens[$stackPtr]['scope_closer']; // If the opener is set, the closer will be too.
            $this->lastSeenPtr = $end;
        } else {
            // Will never be false as Namespaces::getDeclaredName() would have returned false otherwise.
            $start = ($phpcsFile->findNext(Collections::namespaceDeclarationClosers(), ($stackPtr + 1)) + 1);
            $end   = null;
        }

        if ($this->seenInFile[$this->currentNamespacePtr]['name'] === '') {
            // Close the previous namespace.
            $this->seenInFile[$this->currentNamespacePtr]['end'] = ($start - 1);
        } else {
            // Close the previous unscoped namespace.
            $this->seenInFile[$this->currentNamespacePtr]['end'] = ($stackPtr - 1);

            // Record the token in the (unscoped) namespace declaration statement itself as global.
            $this->seenInFile[++$this->currentNamespacePtr] = [
                'start' => $stackPtr,
                'end'   => ($start - 1),
                'name'  => '',
            ];
        }

        // Record the namespace currently being declared.
        $this->seenInFile[++$this->currentNamespacePtr] = [
            'start' => $start,
            'end'   => $end,
            'name'  => $name,
        ];

        // If this is a scoped namespace, record the "global" namespace after it.
        if ($end !== null) {
            $this->seenInFile[++$this->currentNamespacePtr] = [
                'start' => ($end + 1),
                'end'   => null,
                'name'  => '',
            ];
        }
    }

    /**
     * Get the applicable namespace info for an arbitrary stackPtr within the current file.
     *
     * @since 1.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int                         $stackPtr  The token to get the namespace info for.
     *
     * @return array<string, int|string|null> An array with the following keys:
     *                                        - 'start' int      Stack pointer to the effective start of the namespace.
     *                                        - 'end'   int|null Stack pointer to the end of the namespace or
     *                                                           NULL if the end of the namespace is unknown
     *                                                           (unscoped namespace).
     *                                        - 'name'  string   Namespace name.
     *
     * @throws \PHPCSUtils\Exceptions\TypeError           If the $stackPtr parameter is not an integer.
     * @throws \PHPCSUtils\Exceptions\OutOfBoundsStackPtr If the token passed does not exist in the $phpcsFile.
     */
    public function getNamespaceInfo(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();
        if (\is_int($stackPtr) === false) {
            throw TypeError::create(2, '$stackPtr', 'integer', $stackPtr);
        }

        if (isset($tokens[$stackPtr]) === false) {
            throw OutOfBoundsStackPtr::create(2, '$stackPtr', $stackPtr);
        }

        // Have we seen this file yet ?
        $fileName = $phpcsFile->getFilename();
        if ($fileName !== $this->currentFile) {
            $this->reset();
            $this->currentFile = $fileName;
        }

        /*
         * If this file hasn't been tracked yet, we'll have to track it now...
         */
        if ($this->lastSeenPtr !== -1 && $this->lastSeenPtr < $stackPtr) {
            /*
             * Performance tweak: Was the last seen tracking token in a curly brace scope ?
             * If so, a namespace declaration can never be nested,
             * so we can ignore nested tokens when passed to the tracker.
             */
            $scopeOpeners = Tokens::$scopeOpeners;
            unset($scopeOpeners[\T_NAMESPACE]); // Disregard namespace scopes as that could interfer.

            $closedScopePtr = Conditions::getFirstCondition($phpcsFile, $this->lastSeenPtr, $scopeOpeners);
            if ($closedScopePtr !== false
                && isset($tokens[$closedScopePtr]['scope_closer'])
            ) {
                $this->lastSeenPtr = $tokens[$closedScopePtr]['scope_closer'];
            }
        }

        if ($this->lastSeenPtr < $stackPtr) {
            /*
             * The stackPtr is still after the last seen token.
             * Make up the difference between the last seen token and the current token,
             * while skipping over as much code as we safely can.
             */
            for ($i = ($this->lastSeenPtr + 1); $i <= $stackPtr; $i++) {
                if (isset($this->backfillTrackTargets[$tokens[$i]['code']])) {
                    $this->track($phpcsFile, $i);
                    $i = $this->lastSeenPtr;
                }
            }

            // Remember that we walked up to this point.
            if ($this->lastSeenPtr < $stackPtr) {
                $this->lastSeenPtr = $stackPtr;
            }
        }

        /*
         * We can now be sure the file has been tracked up to this point. Find the applicable namespace.
         */
        $return = $this->seenInFileDefault[0]; // Just in case.

        foreach ($this->seenInFile as $info) {
            if ($stackPtr >= $info['start']
                && ($info['end'] === null || $stackPtr <= $info['end'])
            ) {
                $return = $info;
                break;
            }
        }

        return $return;
    }

    /**
     * Get the applicable namespace for an arbitrary stackPtr within the current file.
     *
     * @since 1.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int                         $stackPtr  The token to get the namespace name for.
     *
     * @return string Full namespace name or an empty string for global namespace.
     *
     * @throws \PHPCSUtils\Exceptions\TypeError           If the $stackPtr parameter is not an integer.
     * @throws \PHPCSUtils\Exceptions\OutOfBoundsStackPtr If the token passed does not exist in the $phpcsFile.
     */
    public function getNamespace(File $phpcsFile, $stackPtr)
    {
        return $this->getNamespaceInfo($phpcsFile, $stackPtr)['name'];
    }
}
