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
use PHPCSUtils\ContextTracking\NamespaceTracker;
use PHPCSUtils\ContextTracking\Tracker;
use PHPCSUtils\Exceptions\TypeError;
use PHPCSUtils\Exceptions\ValueError;
use PHPCSUtils\Utils\Conditions;
use PHPCSUtils\Utils\FunctionDeclarations;
use PHPCSUtils\Utils\NamingConventions;
use PHPCSUtils\Utils\Scopes;

/**
 * Declared functions tracker.
 *
 * Keeps track of all named global/namespaced functions - NOT methods! - declared in a file.
 *
 * This is useful for sniffs which, for instance, look for specific callbacks and then need to find
 * the function declaration to check the signature of the function being called (providing the callback
 * and the function declaration are in the same file).
 *
 * Note: This is an "expensive" tracker. Only use this tracker if you really need to know all functions defined
 * in a file.
 * Having said that, if your sniff, as a matter of course, needs to search for declared functions in a file,
 * this tracker will likely be more efficient (and more accurate) than trying to do this yourself.
 *
 * Example code:
 * ```php
 * namespace YourNamespace\Sniffs\Category;
 *
 * use PHPCSUtils\ContextTracking\DeclaredFunctionsTracker;
 *
 * class MySniff implements Sniff
 * {
 *     private $functionTracker;
 *
 *     public function __construct()
 *     {
 *         $this->functionTracker = DeclaredFunctionsTracker::getInstance();
 *     }
 *
 *     public function register()
 *     {
 *         $targets = $this->functionTracker->getTargetTokens();
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
 *         $this->functionTracker->track($phpcsFile, $stackPtr);
 *
 *         // Do your own processing.
 *
 *         // You can now use the `DeclaredFunctionsTracker::findInFile()` method to find any function
 *         // (not method!) declared in the file.
 *         // `DeclaredFunctionsTracker::findInFile()` will return the stack pointer to the function declaration
 *         // or FALSE if the function is not declared in the file.
 *
 *         $fnPtr = $this->functionTracker->findInFile($phpcsFile, $fqnFunctionName);
 *         if (is_int($fnPtr)) {
 *             // Do something.
 *         }
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
final class DeclaredFunctionsTracker implements Tracker
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
     * Keep track of all function declarations seen in this file.
     *
     * {@internal The T_FUNCTION tokens in this array may not necessarily be token pointers to global/namespaced
     * functions. The method tokens will only be filtered out when the list is being resolved (performance tweak).}
     *
     * @since 1.1.0
     *
     * @var array<int> List of stack pointers to T_FUNCTION tokens.
     */
    private $seenInFile;

    /**
     * List of all global and namespaced functions declared within the current file.
     *
     * @since 1.1.0
     *
     * @var array<string, int>|null Key is the FQN function name, value is the stackPtr to the T_FUNCTION
     *                              token declaring the function.
     *                              NULL if the function list hasn't been retrieved yet.
     */
    private $seenInFileResolved;

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
     * @var \PHPCSUtils\ContextTracking\DeclaredFunctionsTracker
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
     * @return \PHPCSUtils\ContextTracking\DeclaredFunctionsTracker
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

        /*
         * Set up the token array for the tokens to track when backfilling a file.
         * Unfortunately we can barely skip anything for this tracker, but the namespace tracker can
         * still benefit from skipping.
         */
        $targets  = $this->getTargetTokens();
        $targets += Tokens::$scopeOpeners;
        $targets += Tokens::$parenthesisOpeners;
        $targets += [
            \T_OPEN_SHORT_ARRAY     => \T_OPEN_SHORT_ARRAY,
            // The below are the tokens relevant for skipping in the context of this tracker.
            \T_DOC_COMMENT_OPEN_TAG => \T_DOC_COMMENT_OPEN_TAG,
            \T_ATTRIBUTE            => \T_ATTRIBUTE,
            \T_START_HEREDOC        => \T_START_HEREDOC,
            \T_START_NOWDOC         => \T_START_NOWDOC,
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
        $this->seenInFileResolved = null;
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
        $targets              = $this->nsContext->getTargetTokens();
        $targets[\T_FUNCTION] = \T_FUNCTION;

        return $targets;
    }

    /**
     * Track the functions found in the file.
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

        if ($this->lastSeenPtr >= $stackPtr) {
            /*
             * Don't do anything if this token has been handled already.
             * This is possible:
             * - when we've previously skipped to the end of something;
             * - when a sniff has already called the `getFunctions()` method.
             */
            return;
        }

        $this->trackIt($phpcsFile, $stackPtr);
    }

    /**
     * Track the functions found in the file (without checking file context, efficiency tweak for backfilling).
     *
     * This method fills the properties with relevant information for examining the function context.
     *
     * @since 1.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the token was found.
     * @param int                         $stackPtr  The current position in the PHP_CodeSniffer file.
     *
     * @return void
     */
    private function trackIt(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (\is_int($stackPtr) === false || isset($tokens[$stackPtr]) === false) {
            // Invalid stack pointer, nothing to track. Ignore.
            return;
        }

        $this->nsContext->track($phpcsFile, $stackPtr);

        // Record the last seen stackPtr.
        $this->lastSeenPtr = $stackPtr;

        /*
         * No need to look any further unless this is a T_FUNCTION token.
         *
         * Unfortunately, there is very little we can skip, but skip what we can.
         */
        if ($tokens[$stackPtr]['code'] !== \T_FUNCTION) {
            if (($tokens[$stackPtr]['code'] === \T_START_HEREDOC
                || $tokens[$stackPtr]['code'] === \T_START_NOWDOC)
                && isset($tokens[$stackPtr]['scope_closer'])
            ) {
                $this->lastSeenPtr = $tokens[$stackPtr]['scope_closer'];
                return;
            }

            if (($tokens[$stackPtr]['code'] === \T_HEREDOC
                || $tokens[$stackPtr]['code'] === \T_NOWDOC)
            ) {
                $owner = Conditions::getLastCondition($phpcsFile, $stackPtr);
                if (isset($tokens[$owner]['scope_closer'])) {
                    $this->lastSeenPtr = $tokens[$owner]['scope_closer'];
                }
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
         * Handle a function declaration.
         *
         * At this point, we just register the function stack pointer, no need to do any further
         * checks until the function list is actually retrieved.
         */
        $this->seenInFile[] = $stackPtr;
    }

    /**
     * Backfill the list of functions if we've not yet seen the complete file.
     *
     * @since 1.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file.
     *
     * @return void
     */
    private function backfill(File $phpcsFile)
    {
        // Have we seen this file yet ?
        $fileName = $phpcsFile->getFilename();
        if ($fileName !== $this->currentFile) {
            $this->reset();
            $this->currentFile = $fileName;
        } elseif (isset($this->seenInFileResolved)) {
            // The functions list has been retrieved before.
            return;
        }

        $tokens    = $phpcsFile->getTokens();
        $lastToken = ($phpcsFile->numTokens - 1);

        /*
         * If this file hasn't been tracked yet, we'll have to track it now...
         */
        if ($this->lastSeenPtr < $lastToken) {
            /*
             * We've not scanned the whole file yet.
             * Make up the difference between the last seen token and the end of file,
             * while skipping over as much code as we safely can (which is very little for
             * this tracker, though more for the namespace tracker, which is used by this tracker).
             */
            for ($i = ($this->lastSeenPtr + 1); $i <= $lastToken; $i++) {
                if (isset($this->backfillTrackTargets[$tokens[$i]['code']])) {
                    $this->trackIt($phpcsFile, $i);
                    $i = $this->lastSeenPtr;
                }
            }

            // Remember that we walked up to this point.
            if ($this->lastSeenPtr < $lastToken) {
                $this->lastSeenPtr = $lastToken;
            }
        }

        /*
         * We can now be sure the file has been fully tracked. Let's create the functions list.
         */
        if ($this->seenInFile === []) {
            // No functions found in the file.
            $this->seenInFileResolved = [];
            return;
        }

        $this->seenInFileResolved = [];

        foreach ($this->seenInFile as $stackPtr) {
            if (Scopes::isOOMethod($phpcsFile, $stackPtr) === true) {
                // We're only concerned with functions, not methods.
                continue;
            }

            $name = FunctionDeclarations::getName($phpcsFile, $stackPtr);
            if (empty($name) === true) {
                // Parse error/live coding.
                break;
            }

            $currentNamespace = $this->nsContext->getNamespace($phpcsFile, $stackPtr);
            if ($currentNamespace === '') {
                $fqname = '\\' . $name;
            } else {
                $fqname = '\\' . $currentNamespace . '\\' . $name;
            }

            $this->seenInFileResolved[$fqname] = $stackPtr;
        }
    }

    /**
     * Get a list of all functions declared in the file.
     *
     * @since 1.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file.
     *
     * @return array<string, int> Key is the FQN function name, value is the stackPtr to the T_FUNCTION
     *                            token declaring the function.
     *                            This may be an empty array if no global/namespaced functions were declared in the file.
     */
    public function getFunctions(File $phpcsFile)
    {
        $this->backfill($phpcsFile);
        return $this->seenInFileResolved;
    }

    /**
     * Find a function declaration in the current file.
     *
     * @since 1.1.0
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile       The PHP_CodeSniffer file.
     * @param string                      $fqnFunctionName The fully qualified name of the function to search for.
     *
     * @return int|false Stack pointer to the T_FUNCTION token starting the function declaration;
     *                   or FALSE if the function is not declared in this file.
     *
     * @throws \PHPCSUtils\Exceptions\TypeError  If the $fqnFunctionName parameter is not a string.
     * @throws \PHPCSUtils\Exceptions\ValueError If the passed function name is an empty string.
     * @throws \PHPCSUtils\Exceptions\ValueError If the passed function name is not a fully qualified name.
     */
    public function findInFile(File $phpcsFile, $fqnFunctionName)
    {
        if (\is_string($fqnFunctionName) === false) {
            throw TypeError::create(2, '$fqnFunctionName', 'string', $fqnFunctionName);
        }

        if ($fqnFunctionName === '') {
            throw ValueError::create(2, '$fqnFunctionName', 'must be a non-empty string');
        }

        if ($fqnFunctionName[0] !== '\\') {
            $msg = \sprintf('must be a fully qualified function name; received unqualified name: %s', $fqnFunctionName);
            throw ValueError::create(2, '$fqnFunctionName', $msg);
        }

        $this->backfill($phpcsFile);

        if (isset($this->seenInFileResolved[$fqnFunctionName])) {
            // Name is provided in the same case as declared.
            return $this->seenInFileResolved[$fqnFunctionName];
        }

        foreach ($this->seenInFileResolved as $name => $ptr) {
            if (NamingConventions::isEqual($name, $fqnFunctionName) === true) {
                return $ptr;
            }
        }

        return false;
    }
}
