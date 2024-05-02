<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\ContextTracking\NamespaceTracker;

use PHPCSUtils\ContextTracking\NamespaceTracker;
use PHPCSUtils\Tests\AssertPropertySame;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Testcase for the \PHPCSUtils\ContextTracking\NamespaceTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\NamespaceTracker
 *
 * @since 1.1.0
 */
abstract class NamespaceTrackerTestCase extends UtilityMethodTestCase
{
    use AssertPropertySame;

    /**
     * Test that the track() method sets the class properties correctly.
     *
     * @dataProvider dataTrackSetsProperties
     *
     * @param string                                                               $marker   The comment which prefaces the
     *                                                                                       test target token in the
     *                                                                                       test file.
     * @param int|string                                                           $target   The token constant to search
     *                                                                                       for to find the target token.
     * @param array<string, string|int|array<int, array<string, int|string|null>>> $expected The expected property values
     *                                                                                       at that point in the file.
     *
     * @return void
     */
    public function testTrackSetsProperties($marker, $target, $expected)
    {
        $tracker        = NamespaceTracker::getInstance();
        $trackerTargets = $tracker->getTargetTokens();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        $tokens   = self::$phpcsFile->getTokens();
        $stackPtr = $this->getTargetToken($marker, $target);

        for ($i = 0; $i <= $stackPtr; $i++) {
            if (isset($trackerTargets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // Test that the tracker has tracked the namespace correctly.
        $this->assertPropertySame(
            $expected['file'],
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is as expected'
        );
        $this->assertPropertySame(
            $expected['lastSeenPtr'],
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is as expected'
        );
        $this->assertPropertySame(
            $expected['currentNSPtr'],
            'currentNamespacePtr',
            $tracker,
            'Failed asserting that the currentNamespacePtr property is as expected'
        );
        $this->assertPropertySame(
            $expected['seenInFile'],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is as expected'
        );
    }

    /**
     * Data provider.
     *
     * @see testTrackSetsProperties() For the array format.
     *
     * @return array<string, array<string, string|int|array<string, string|int|array<int, array<string, int|string|null>>>>>
     */
    abstract public static function dataTrackSetsProperties();

    /**
     * Test retrieving the namespace for a token before the last seen token when the whole file has been scanned.
     *
     * @dataProvider dataGetNamespace
     *
     * @param string                         $marker   The comment which prefaces the test target token in the test file.
     * @param int|string                     $target   The token constant to search for to find the target token.
     * @param array<string, int|string|null> $expected The expected namespace info at that point in the file.
     *
     * @return void
     */
    public function testGetNamespaceForTokenBeforeLastSeen($marker, $target, $expected)
    {
        $tracker        = NamespaceTracker::getInstance();
        $trackerTargets = $tracker->getTargetTokens();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        $tokens   = self::$phpcsFile->getTokens();
        $stackPtr = $this->getTargetToken($marker, $target);

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($trackerTargets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        $this->assertSame($expected['name'], $tracker->getNamespace(self::$phpcsFile, $stackPtr));
    }

    /**
     * Test retrieving the namespace for the "current" stackPtr when the file has been scanned up to the "current" token.
     *
     * @dataProvider dataGetNamespace
     *
     * @param string                         $marker   The comment which prefaces the test target token in the test file.
     * @param int|string                     $target   The token constant to search for to find the target token.
     * @param array<string, int|string|null> $expected The expected namespace info at that point in the file.
     *
     * @return void
     */
    public function testGetNamespaceInfoForCurrentToken($marker, $target, $expected)
    {
        $tracker        = NamespaceTracker::getInstance();
        $trackerTargets = $tracker->getTargetTokens();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        $tokens   = self::$phpcsFile->getTokens();
        $stackPtr = $this->getTargetToken($marker, $target);

        for ($i = 0; $i <= $stackPtr; $i++) {
            if (isset($trackerTargets[$tokens[$i]['code']]) === true
                || $tokens[$i]['code'] === $target
            ) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        $this->assertSame($expected, $tracker->getNamespaceInfo(self::$phpcsFile, $stackPtr));
    }

    /**
     * Data provider.
     *
     * @see testGetNamespaceForTokenBeforeLastSeen() For the array format.
     * @see testGetNamespaceForCurrentToken()        For the array format.
     *
     * @return array<string, array<string, int|string|array<string, int|string|null>>>
     */
    abstract public static function dataGetNamespace();

    /**
     * Test retrieving the namespace for a token *after* the last seen token (and some before).
     *
     * @dataProvider dataGetNamespaceArbitraryToken
     *
     * @param string     $marker       The comment which prefaces the test target token in the test file.
     * @param int|string $target       The token constant to search for to find the target token.
     * @param string     $expected     The expected namespace name at that point in the file.
     * @param string     $stopAt       Comment prefacing a token to stop the tracking at.
     * @param int|string $stopAtTarget The token constant for the "stop at" token.
     *
     * @return void
     */
    public function testGetNamespaceArbitraryToken($marker, $target, $expected, $stopAt, $stopAtTarget)
    {
        $tracker        = NamespaceTracker::getInstance();
        $trackerTargets = $tracker->getTargetTokens();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        $tokens    = self::$phpcsFile->getTokens();
        $stopAtPtr = $this->getTargetToken($stopAt, $stopAtTarget);

        for ($i = 0; $i <= $stopAtPtr; $i++) {
            if (isset($trackerTargets[$tokens[$i]['code']]) === true
                || $i === $stopAtPtr
            ) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        $stackPtr = $this->getTargetToken($marker, $target);
        $this->assertSame($expected, $tracker->getNamespace(self::$phpcsFile, $stackPtr));
    }

    /**
     * Data provider.
     *
     * @see testGetNamespaceArbitraryToken() For the array format.
     *
     * @return array<string, array<string, string|int>>
     */
    abstract public static function dataGetNamespaceArbitraryToken();

    /**
     * Helper function to get a subset of the array items from a "seenInFile" array.
     *
     * @param int  $offset           The offset to start the subset at.
     * @param int  $length           The number of items to retrieve.
     * @param bool $setLastEndToNull Whether or not to replace the "end" key in the last item in the subset with `null`.
     *
     * @return array<int, array<string, int|string|null>>
     */
    protected static function getSeenInFileSubset($offset, $length, $setLastEndToNull = false)
    {
        $seenInFile = static::getSeenInFile();
        $subset     = \array_slice($seenInFile, $offset, $length, true);

        if ($setLastEndToNull === true) {
            $lastKey                 = \key(\array_slice($subset, -1, 1, true));
            $subset[$lastKey]['end'] = null;
        }

        return $subset;
    }

    /**
     * Helper function defining the "seenInFile" array.
     *
     * @return array<int, array<string, int|string|null>>
     */
    abstract protected static function getSeenInFile();
}
