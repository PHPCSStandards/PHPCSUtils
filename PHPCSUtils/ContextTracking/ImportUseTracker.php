<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\ContextTracking;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\ContextTracking\NamespaceTracker;
use PHPCSUtils\ContextTracking\Tracker;
use PHPCSUtils\Exceptions\OutOfBoundsStackPtr;
use PHPCSUtils\Exceptions\TypeError;
use PHPCSUtils\Utils\Conditions;
use PHPCSUtils\Utils\UseStatements;

/**
 * Import use statement tracker.
 *
 * This tracker is intended to allow for finding if there is an active import use statement for
 * any "name" used inline in the code.
 *
 * This tracker is not intended for sniffs which want to _examine_ import use statements. Those sniffs
 * should listen to the `T_USE` token themselves, though, that can, of course, be combined with using
 * this tracker.
 *
 * Example code:
 * ```php
 * namespace YourNamespace\Sniffs\Category;
 *
 * use PHPCSUtils\ContextTracking\ImportUseTracker;
 *
 * class MySniff implements Sniff
 * {
 *     private $useContext;
 *
 *     public function __construct()
 *     {
 *         $this->useContext = ImportUseTracker::getInstance();
 *     }
 *
 *     public function register()
 *     {
 *         $targets = $this->useContext->getTargetTokens();
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
 *         $this->useContext->track($phpcsFile, $stackPtr);
 *
 *         // Do your own processing.
 *
 *         // You can now use the `ImportUseTracker::getUseStatements()` method to get access to the applicable
 *         // import use statements at any point within the current file.
 *         // Note: the tracker has no opinion on whether use statements are relevant for the `$stackPtr` !
 *         $currentUseStatements = $this->useContext->getUseStatements($phpcsFile, $stackPtr);
 *     }
 * }
 * ```
 *
 * Pro-tip: this Tracker uses the {@see PHPCSUtils\ContextTracking\NamespaceTracker}, so if your sniff _also_
 * uses the `NamespaceTracker`, you only need to call the `track()` method for this Tracker.
 * The `track()` method for the `NamespaceTracker` will be invoked automatically by this Tracker.
 *
 * @since 1.1.0
 */
final class ImportUseTracker implements Tracker
{

/*
TODO: make sure this class keeps track of first and last pointer of each use statement to allow for replacing.
Also keep track of the end token of the last seen use statement to allow for adding at end.
*/

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
     * Keep track of which T_USE tokens were seen per namespace.
     *
     * {@internal The T_USE tokens in these arrays may not necessarily be token pointers for import use statements.
     * The non-import use tokens will only be filtered out when the statements are being resolved (performance tweak).}
     *
     * @since 1.1.0
     *
     * @var array<int, array<int>> Key is the token pointer to the effective start of a namespace.
     *                             Value is an array with stack pointers to T_USE tokens seen in that namespace.
     */
    private $seenInFile;

    /**
     * Resolved import use statements per namespace.
     *
     * @since 1.1.0
     *
     * @var array<int, array<string, int|array<string, array<string, string>>|null>>
     *            Key is the token pointer to the effective start of a namespace.
     *            Value is an array with two keys:
// Change to lastResolved + lastProcessed
// Do not include in the return array ?
// Maybe: has a useTokens array with the stack pointer to all resolved T_USE tokens included in the result (only import use)
// Possibly, this array should be in the format `int T_USE ptr => int end of statement ptr`
     *            - 'lastPtr'       int|null                               Stack pointer to the T_USE token for the last
     *                                                                     use statement  for this namespace examined for
     *                                                                     inclusion in the statements array.
     *                                                                     Note: This could be the pointer to a
     *                                                                     trait/closure use statement.
     *                                                                     NULL if no statements were resolved.
     *            - 'statements'    array<string, array<string, string>>   The resolved import use statements.
     *                                                                     See {@see UseStatements::splitImportUseStatement()}
     *                                                                     for more details about the array format.
     *            - 'effectiveFrom' int|null                               Stack pointer to the point in the file where the
     *                                                                     last resolved import use statement takes effect.
     *                                                                     NULL if no statements were resolved.
     */
    private $seenInFileResolved;

    /**
     * Default value for the $useImportStatements property.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @since 1.1.0
     *
     * @var array<string, array<string, string>>
     */
    private $useImportStatementsDefault = [
        'name'     => [],
        'function' => [],
        'const'    => [],
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
     * @var \PHPCSUtils\ContextTracking\ImportUseTracker
     */
    private static $instance;

    /**
     * Singleton instance of the Namespace tracker.
     *
     * @since 1.1.0
     *
     * @var \PHPCSUtils\ContextTracking\NamespaceTracker
     */
    private $nsContext;

    /**
     * Get the tracker instance.
     *
     * @since 1.1.0
     *
     * @return \PHPCSUtils\ContextTracking\ImportUseTracker
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
        $this->nsContext = NamespaceTracker::getInstance();

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
        $this->currentFile        = '';
        $this->lastSeenPtr        = -1;
        $this->seenInFile         = [];
        $this->seenInFileResolved = [];
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
        $targets         = $this->nsContext->getTargetTokens();
        $targets[\T_USE] = \T_USE;

        return $targets;
    }

    /**
     * Track the import use statements context.
     *
     * This method fills the properties with relevant information for examining the use statement context.
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
             * - when we've previously skipped to the end of something, like a closed scope;
             * - when a sniff has already called the `getUseStatements[Info]()` method for a later token;
             * - or when the tracker has been injected into another tracker.
             */
            return;
        }

        $this->nsContext->track($phpcsFile, $stackPtr);

        // Record the last seen stackPtr.
        $this->lastSeenPtr = $stackPtr;

        /*
         * No need to look any further unless this is a T_USE token.
         *
         * On the off-change that a function/class/array etc token has been passed,
         * check for a closer, as an import use declaration can never be nested, except inside
         * a scoped namespace declaration, so we can ignore nested tokens when passed to the tracker.
         */
        if ($tokens[$stackPtr]['code'] !== \T_USE) {
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

        /*
         * Handle a potential import use statement.
         */

        // Get current namespace.
        $nsStart = $this->nsContext->getNamespaceInfo($phpcsFile, $stackPtr)['start'];

        // Register the use token to the namespace.
/*
        if (isset($this->seenInFile[$nsStart]) === false) {
            $this->seenInFile[$nsStart] = [];
        }
*/
        $this->seenInFile[$nsStart][] = $stackPtr;
/*




// Remove anything namespacey in favour of the tracker, use getNamespace() to retrieve current namespace.
// Can we do this as soon as a namespace token was seen ?

        /*
         * Reset the namespace and use statements if the current token is beyond the namespace end pointer
         * (applies to scoped namespaces only).
         * /
        if (isset($this->currentNamespaceEndPtr)
            && $stackPtr >= $this->currentNamespaceEndPtr
        ) {
            $this->seenInFile[$this->currentNamespaceStartPtr] = [
                'end'  => $this->currentNamespaceEndPtr,
                'name' => $this->currentNamespace,
            ];

            $this->currentNamespace         = '';
            $this->currentNamespaceStartPtr = ($this->currentNamespaceEndPtr + 1);
            $this->currentNamespaceEndPtr   = null;
            $this->useImportStatements    = $this->useImportStatementsDefault;
        }

        $tokens = $phpcsFile->getTokens();

// Can we do something with scope closer ?
// i.e. if current token has scope closer, set last seen to scope closer as import use can never be within a scope (except for a namespace scope)


// Use import statements should be stored per namespace
// but import statement don't have to be placed directly after the namespace, so we should probably also store an "applicable from" token position.

// Only collect pointers, no need to read and parse the statements until they are being requested

        switch ($tokens[$stackPtr]['code']) {
            /*
             * Retrieve and store the current namespace name.
             * /
            case \T_NAMESPACE:
                if ($this->currentNamespace !== ''
                    && isset($this->currentNamespaceEndPtr)
                    && $stackPtr < $this->currentNamespaceEndPtr
                ) {
                    // We already know the current namespace, move along, nothing to see here.
                    break;
                }

                $name = Namespaces::getDeclaredName($phpcsFile, $stackPtr);
                if ($name === false) {
                    break;
                }

                // Set name and potential end pointer.
                $this->currentNamespace = $name;

                if (isset($this->tokens[$stackPtr]['scope_closer']) === true) {
                    // Scoped namespace.
                    $this->currentNamespaceEndPtr = $this->tokens[$stackPtr]['scope_closer'];
                } else {
                    $this->currentNamespaceEndPtr = $phpcsFile->numTokens;
                }

                break;

            /*
             * Retrieve and store information on all `use` import statements.
             * /
            case \T_USE:
                $this->useImportStatements = UseStatements::splitAndMergeImportUseStatement(
                    $phpcsFile,
                    $stackPtr,
                    $this->useImportStatements
                );

                break;
        }
*/
    }

    /**
     * Get the applicable import use statements info for an arbitrary stackPtr within the current file.
     *
     * @since 1.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int                         $stackPtr  The token to get the applioable use statements for.
     *
// TODO: update for changes
     * @return array<string, int|array<string, array<string, string>>|null> An array with the following keys:
     *             - 'lastPtr'       int|null                               Stack pointer to the T_USE token for the last
     *                                                                      use statement examined for inclusion in the
     *                                                                      statements array.
     *                                                                      Note: This could be the pointer to a
     *                                                                      trait/closure use statement.
     *                                                                      NULL if no statements were resolved.
     *             - 'statements'    array<string, array<string, string>>   Use statements array.
     *                                                                      See {@see UseStatements::splitImportUseStatement()}
     *                                                                      for more details about the array format.
     *             - 'effectiveFrom' int|null                               Stack pointer to the point in the file where the
     *                                                                      last resolved import use statement takes effect.
     *                                                                      NULL if no statements were resolved.
     *
     * @throws \PHPCSUtils\Exceptions\TypeError           If the $stackPtr parameter is not an integer.
     * @throws \PHPCSUtils\Exceptions\OutOfBoundsStackPtr If the token passed does not exist in the $phpcsFile.
     */
    public function getUseStatementsInfo(File $phpcsFile, $stackPtr)
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
             * If so, an import use declaration can never be nested, except in a scoped namespace
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
When retrieving parse & merge the statements and store for later use.

// Better: instead of storing per use statement - store per namespace.
// Needs to take namespace into account !
*/


// Change to lastPtr to lastResolved
// Do not include in the return array ?
// Maybe: has a useTokens array with the stack pointer to all resolved T_USE tokens included in the result (only import use)
// Possibly, this array should be in the format `int T_USE ptr => int end of statement ptr`


        /*
         * We can now be sure the file has been tracked up to this point.
         * Find and resolve the applicable set of import use statements.
         */
        $nsStart       = $this->nsContext->getNamespaceInfo($phpcsFile, $stackPtr)['start'];
        $skipUpTo      = null;
        $lastResolved  = null;
        $lastProcessed = null;
        $effectiveFrom = null;
//        $pointers      = [];
        $statements    = $this->useImportStatementsDefault;

        if (isset($this->seenInFileResolved[$nsStart])
            && $this->seenInFileResolved[$nsStart]['effectiveFrom'] <= $stackPtr
        ) {
            $skipUpTo      = $this->seenInFileResolved[$nsStart]['lastPtr'];
            $lastResolved  = $this->seenInFileResolved[$nsStart]['lastPtr'];
            $statements    = $this->seenInFileResolved[$nsStart]['statements'];
            $effectiveFrom = $this->seenInFileResolved[$nsStart]['effectiveFrom'];
        }

        if (isset($this->seenInFile[$nsStart]) && $this->seenInFile[$nsStart] !== []) {
            foreach ($this->seenInFile[$nsStart] as $k => $usePtr) {
                if ($usePtr >= $stackPtr) {
                    // No need to process use statements which aren't in effect for the stackPtr.
                    break;
                }

                if (isset($skipUpTo) && $usePtr <= $skipUpTo) {
                    // Already resolved, skip it.
                    continue;
                }

                $lastProcessed = $usePtr;
//                $lastResolved  = $usePtr;

                if (UseStatements::isImportUse($phpcsFile, $usePtr) === false) {
					// Prevent checking this token again if the statements need to be resolved a second time.
					unset($this->seenInFile[$nsStart][$k]);
                    continue;
                }

                $endOfStatement = $phpcsFile->findNext([\T_SEMICOLON, \T_CLOSE_TAG], ($usePtr + 1));
                if ($endOfStatement === false) {
                    // Live coding/parse error.
//                    $lastProcessed = $usePtr;
                    break;
                }

                if ($stackPtr <= $endOfStatement) {
                    // This is a token _within_ the import use statement. This statement is not yet in effect.
                    break;
                }

                $pointers[$usePtr] = [
                	'end'      => $endOfStatement,
                	'resolved' => self::splitImportUseStatement($phpcsFile, $usePtr),
                ];

//                $statements    = UseStatements::splitAndMergeImportUseStatement($phpcsFile, $usePtr, $statements);
//                $effectiveFrom = ++$endOfStatement;
            }

// Store the final result somewhere and re-use if last ptr is the same ?

            foreach ($pointers as $usePtr => $info) {
				$statements = UseStatements::mergeImportUseStatements($statements, $info['resolved']);
			}

			$effectiveFrom = ($info['end'] + 1);
        }

/*
While this is efficient for the most common case of "all use statements at the start of a file",
this is not efficient for when the stackPtr is before the last import use statement.

Need to have a good think about how to deal with that better. Then again, the resolving is cached in the Split* function anyway, so who cares ?
*/

/*
CURRENT:
Should we have a "lastSeen" vs "lastResolved" array key (where lastSeen is not included in the return value, but only for internal use ?)
- lastSeen should be used for the skipping of resolving
- but lastResolved is more useful info for users of the class

Then again: maybe the "is import use" check should be moved to the tracker as having to deal with trait/closure use here, just complicates things.
*/

        $return = [
            'lastPtr'       => $lastResolved,
            'statements'    => $statements,
            'effectiveFrom' => $effectiveFrom,
        ];

        // Only replace previously stored resolved statements if we walked further than before.
        if (isset($this->seenInFileResolved[$nsStart]) === false
            || (isset($skipUpTo, $lastResolved) && $lastResolved > $skipUpTo)
        ) {
            $this->seenInFileResolved[$nsStart] = $return;
        }

        return $return;
    }

    /**
     * Get the applicable import use statements for an arbitrary stackPtr within the current file.
     *
     * @since 1.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int                         $stackPtr  The token to get the applicable use statements for.
     *
     * @return array<string, array<string, string>> Use statements array.
     *                                              See {@see UseStatements::splitImportUseStatement()}
     *                                              for more details about the array format.
     *
     * @throws \PHPCSUtils\Exceptions\TypeError           If the $stackPtr parameter is not an integer.
     * @throws \PHPCSUtils\Exceptions\OutOfBoundsStackPtr If the token passed does not exist in the $phpcsFile.
     */
    public function getUseStatements(File $phpcsFile, $stackPtr)
    {
        return $this->getUseStatementsInfo($phpcsFile, $stackPtr)['statements'];
    }
}
