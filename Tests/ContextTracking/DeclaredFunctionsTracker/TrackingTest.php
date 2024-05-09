<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\ContextTracking\DeclaredFunctionsTracker;

use PHPCSUtils\ContextTracking\DeclaredFunctionsTracker;
use PHPCSUtils\Tests\AssertPropertySame;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Testcase for the \PHPCSUtils\ContextTracking\DeclaredFunctionsTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\DeclaredFunctionsTracker
 *
 * @since 1.1.0
 */
final class TrackingTest extends UtilityMethodTestCase
{
    use AssertPropertySame;

    /**
     * List of all the markers for T_FUNCTION tokens in the test case file.
     *
     * @var array<string>
     */
    private $seenMarkers = [
        '/* method1 */',
        '/* method2 */',
        '/* testAttribute */',
        '/* method4 */',
        '/* method5 */',
        '/* method6 */',
        '/* function */',
    ];

    /**
     * List of all the function markers in the test case file and their FQN function name.
     *
     * @var array<string, string>
     */
    private $functionMarkers = [
        '/* function */' => '\globalFunction',
    ];

    /**
     * The expected final value for seenInFile property.
     *
     * @var array<int>
     */
    protected static $expectedSeenInFile = [];

    /**
     * The expected functions list.
     *
     * The functions list returned will be the same, no matter at what point in the
     * file the list is requested.
     *
     * @var array<string, int>
     */
    protected static $expectedFunctions = [];

    /**
     * Set up the expected functions cache for the tests.
     *
     * Retrieves the marker token stack pointer positions only once and caches
     * them as they won't change between the tests anyway.
     *
     * @before
     *
     * @return void
     */
    protected function setUpCaches()
    {
        if (empty(self::$expectedSeenInFile) === true) {
            foreach ($this->seenMarkers as $marker) {
                self::$expectedSeenInFile[] = $this->getTargetToken($marker, [\T_FUNCTION]);
            }
        }

        if (empty(self::$expectedFunctions) === true) {
            foreach ($this->functionMarkers as $marker => $name) {
                self::$expectedFunctions[$name] = $this->getTargetToken($marker, [\T_FUNCTION]);
            }
        }
    }

    /**
     * Safeguard that a call to track() with a non-integer stack pointer does not change the lastSeenPtr.
     *
     * @return void
     */
    public function testNonIntegerStackPtrGetsIgnored()
    {
        $tracker = DeclaredFunctionsTracker::getInstance();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        // Initialize the tracking of a new file.
        $tracker->track(self::$phpcsFile, 5);

        $this->assertPropertySame(
            5,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is 5 to start with'
        );

        // Try tracking a non-integer stackPtr.
        $tracker->track(self::$phpcsFile, false);

        $this->assertPropertySame(
            5,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is unchanged after having been passed a boolean stackPtr'
        );
    }

    /**
     * Safeguard that a call to track() with a stack pointer which doesn't exist in the current file
     * does not change the lastSeenPtr.
     *
     * @return void
     */
    public function testNonExistentStackPtrGetsIgnored()
    {
        $tracker = DeclaredFunctionsTracker::getInstance();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        // Initialize the tracking of a new file.
        $tracker->track(self::$phpcsFile, 5);

        $this->assertPropertySame(
            5,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is 5 to start with'
        );

        // Try tracking a non-existent stackPtr.
        $tracker->track(self::$phpcsFile, 100000);

        $this->assertPropertySame(
            5,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is unchanged after having been passed an invalid stackPtr'
        );
    }

    /**
     * Test that the track() method sets the class properties correctly and that requesting the functions
     * backfills when the file hasn't been completely tracked yet.
     *
     * @dataProvider dataTrackSetsProperties
     *
     * @param string     $marker      The comment which prefaces the test target token to stop tracking at.
     * @param int|string $target      The token constant to search for to find the target token.
     * @param int        $lastSeenPtr The expected property value for the lastSeenPtr.
     * @param int        $seenCount   The number of function tokens expected to have been seen.
     *
     * @return void
     */
    public function testTrackSetsProperties($marker, $target, $lastSeenPtr, $seenCount)
    {
        $tracker        = DeclaredFunctionsTracker::getInstance();
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

        // Test that the tracker has tracked the function declarations correctly.
        $this->assertPropertySame(
            \str_replace('.php', '.inc', __FILE__),
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is as expected'
        );
        $this->assertPropertySame(
            $lastSeenPtr,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is as expected'
        );

        $expectedSeenInFile = \array_slice(self::$expectedSeenInFile, 0, $seenCount);

        $this->assertPropertySame(
            $expectedSeenInFile,
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is as expected'
        );

        // Until the `getFunctions()` method has been called, the seenInFileResolved property should always be null.
        $this->assertPropertySame(
            null,
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property is an empty array'
        );

        // Test that getFunctions() triggers a backfill of the rest of the tokens.
        $this->assertSame(self::$expectedFunctions, $tracker->getFunctions(self::$phpcsFile));

        $this->assertPropertySame(
            (self::$phpcsFile->numTokens - 1),
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is as expected after calling getFunctions()'
        );

        $expectedSeenInFile = \array_slice(self::$expectedSeenInFile, 0, $seenCount);

        $this->assertPropertySame(
            self::$expectedSeenInFile,
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is as expected after calling getFunctions()'
        );

        $this->assertPropertySame(
            self::$expectedFunctions,
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property has been filled after calling getFunctions()'
        );
    }

    /**
     * Data provider.
     *
     * @see testTrackSetsProperties() For the array format.
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataTrackSetsProperties()
    {
        return [
            'Start of file' => [
                'marker'      => '/* testStartOfFile */',
                'target'      => \T_WHITESPACE,
                'lastSeenPtr' => 3,
                'seenCount'   => 0,
            ],
            'Docblock opener skips to closer' => [
                'marker'      => '/* testDocblock */',
                'target'      => \T_DOC_COMMENT_OPEN_TAG,
                'lastSeenPtr' => 25,
                'seenCount'   => 0,
            ],
            'Attributes opener skips to closer' => [
                'marker'      => '/* testAttribute */',
                'target'      => \T_ATTRIBUTE,
                'lastSeenPtr' => 109,
                'seenCount'   => 2,
            ],
            'Token within attributes skips to closer' => [
                'marker'      => '/* testAttribute */',
                'target'      => \T_STRING,
                'lastSeenPtr' => 109,
                'seenCount'   => 2,
            ],
            'Heredoc opener skips to closer' => [
                'marker'      => '/* testHeredoc */',
                'target'      => \T_START_HEREDOC,
                'lastSeenPtr' => 167,
                'seenCount'   => 4,
            ],
            'Token within heredoc skips to closer' => [
                'marker'      => '/* testHeredoc */',
                'target'      => \T_HEREDOC,
                'lastSeenPtr' => 167,
                'seenCount'   => 4,
            ],
            'Nowdoc opener skips to closer' => [
                'marker'      => '/* testNowdoc */',
                'target'      => \T_START_NOWDOC,
                'lastSeenPtr' => 214,
                'seenCount'   => 6,
            ],
            'Token within nowdoc skips to closer' => [
                'marker'      => '/* testNowdoc */',
                'target'      => \T_NOWDOC,
                'lastSeenPtr' => 214,
                'seenCount'   => 6,
            ],
            'End of file' => [
                'marker'      => '/* testEndOfFile */',
                'target'      => \T_WHITESPACE,
                'lastSeenPtr' => 236,
                'seenCount'   => 7,
            ],
        ];
    }

    /**
     * Test that tracking after the file has already been backfilled does not set the lastSeenPtr pointer back.
     *
     * @return void
     */
    public function testLastSeenPtrWillNotBeLowered()
    {
        $tracker = DeclaredFunctionsTracker::getInstance();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        $this->assertSame(self::$expectedFunctions, $tracker->getFunctions(self::$phpcsFile));

        $this->assertPropertySame(
            (self::$phpcsFile->numTokens - 1),
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is as expected after calling getFunctions()'
        );

        $tracker->track(self::$phpcsFile, 0);

        $this->assertPropertySame(
            (self::$phpcsFile->numTokens - 1),
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is still at end of file when tracking is triggered after backfill'
        );
    }
}
