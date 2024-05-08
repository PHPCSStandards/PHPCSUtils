<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\ContextTracking\ImportUseTracker;

use PHPCSUtils\ContextTracking\ImportUseTracker;
use PHPCSUtils\Tests\AssertPropertySame;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\ContextTracking\ImportUseTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\ImportUseTracker
 *
 * @since 1.1.0
 */
final class ResolvedStatementsStorageTest extends UtilityMethodTestCase
{
    use AssertPropertySame;

    /**
     * Full path to the test case file associated with this test class.
     *
     * @var string
     */
    protected static $caseFile = '';

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = __DIR__ . '/SingleUnscopedNamespaceWithImportsTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Verify that if a token is requested after a previous partial resolve, the previously resolved
     * import use statements will be retrieved from cache, added to and the cache updated.
     *
     * @return void
     */
    public function testResolvedStatementsPartiallyStored()
    {
        $tracker        = ImportUseTracker::getInstance();
        $trackerTargets = $tracker->getTargetTokens();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        $tokens   = self::$phpcsFile->getTokens();
        $stackPtr = $this->getTargetToken('/* testMultiImportUse */', \T_USE);

        // Track all import use tokens in the file.
        for ($i = 0; $i <= $stackPtr; $i++) {
            if (isset($trackerTargets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // First request for statements should store a subset of the use statements.
        $stackPtr = $this->getTargetToken('/* testEndOfGroupUse */', \T_CLASS);
        $tracker->getUseStatements(self::$phpcsFile, $stackPtr);

        $original = $this->getObjectPropertyValue($tracker, 'seenInFileResolved');

        // Next request for statements should reuse the stored resolved statements and update the store.
        $stackPtr = $this->getTargetToken('/* testClosureBeforeFirstTrackingToken */', \T_CLOSURE);
        $tracker->getUseStatements(self::$phpcsFile, $stackPtr);

        $update = $this->getObjectPropertyValue($tracker, 'seenInFileResolved');

        $this->assertNotSame($original, $update, 'Resolved statements cache has not been updated');
        $this->assertGreaterThan(
            \count($original, \COUNT_RECURSIVE),
            \count($update, \COUNT_RECURSIVE),
            'Updated resolved statements cache does not contain more information than the original resolved statements cache'
        );
    }

    /**
     * Verify that if a token is requested after a previous full resolve and that token is _before_ the last handled use token,
     * the previously resolved import use statements will be NOT retrieved from cache and the cache will NOT be updated.
     *
     * @return void
     */
    public function testResolvedStatementsCompletelyStored()
    {
        $tracker        = ImportUseTracker::getInstance();
        $trackerTargets = $tracker->getTargetTokens();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        $tokens   = self::$phpcsFile->getTokens();
        $stackPtr = $this->getTargetToken('/* testMultiImportUse */', \T_USE);

        // Track all import use tokens in the file.
        for ($i = 0; $i <= $stackPtr; $i++) {
            if (isset($trackerTargets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // First request for statements should store a the complete set of the use statements.
        $stackPtr = $this->getTargetToken('/* testClosureBeforeFirstTrackingToken */', \T_CLOSURE);
        $tracker->getUseStatements(self::$phpcsFile, $stackPtr);

        $original = $this->getObjectPropertyValue($tracker, 'seenInFileResolved');

        // Next request for statements should NOT reuse the stored resolved statements and NOT update the store.
        $stackPtr = $this->getTargetToken('/* testEndOfGroupUse */', \T_CLASS);
        $tracker->getUseStatements(self::$phpcsFile, $stackPtr);

        $update = $this->getObjectPropertyValue($tracker, 'seenInFileResolved');

        $this->assertSame($original, $update, 'Resolved statements cache was unexpectedly updated');
    }

    /**
     * Verify that if a token is requested after a previous resolve and that token is _after_ the last handled use token,
     * the previously resolved import use statements will be retrieved from cache, added to and the cache updated.
     *
     * @return void
     */
    public function testResolvedStatementsPartiallyTrackedAndPartiallyStored()
    {
        $tracker        = ImportUseTracker::getInstance();
        $trackerTargets = $tracker->getTargetTokens();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        $tokens   = self::$phpcsFile->getTokens();
        $stackPtr = $this->getTargetToken('/* testGroupImportUse */', \T_USE);

        // Track the first two import use tokens in the file.
        for ($i = 0; $i <= $stackPtr; $i++) {
            if (isset($trackerTargets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // First request for statements should store a subset of the use statements.
        $stackPtr = $this->getTargetToken('/* testEndOfGroupUse */', \T_SEMICOLON);
        $tracker->getUseStatements(self::$phpcsFile, $stackPtr);

        $original = $this->getObjectPropertyValue($tracker, 'seenInFileResolved');

        // Next request for statements should reuse the stored resolved statements,
        // backfill the missing tokens and update the store.
        $stackPtr = $this->getTargetToken('/* testClosureBeforeFirstTrackingToken */', \T_VARIABLE);
        $tracker->getUseStatements(self::$phpcsFile, $stackPtr);

        $update = $this->getObjectPropertyValue($tracker, 'seenInFileResolved');

        $this->assertNotSame($original, $update, 'Resolved statements cache has not been updated');
        $this->assertGreaterThan(
            \count($original, \COUNT_RECURSIVE),
            \count($update, \COUNT_RECURSIVE),
            'Updated resolved statements cache does not contain more information than the original resolved statements cache'
        );
    }
}
