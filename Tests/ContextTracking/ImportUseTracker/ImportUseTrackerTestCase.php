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
 * Testcase for the \PHPCSUtils\ContextTracking\ImportUseTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\ImportUseTracker
 *
 * @since 1.1.0
 */
abstract class ImportUseTrackerTestCase extends UtilityMethodTestCase
{
    use AssertPropertySame;

    /**
     * Re-usable use statement info array for the "no statements in effect" case.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<int, array<string, int|array<string, array<string, string>>|null>>
     */
    protected static $noStatementsInfoArray = [
        0 => [
            'lastPtr'       => null,
            'statements'    => [
                'name'     => [],
                'function' => [],
                'const'    => [],
            ],
            'effectiveFrom' => null,
        ],
    ];

    /**
     * Re-usable use statement info sub-array for the "no statements in effect" case.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, int|array<string, array<string, string>>|null>
     */
    protected static $noStatementsInfoSubArray = [
        'lastPtr'       => null,
        'statements'    => [
            'name'     => [],
            'function' => [],
            'const'    => [],
        ],
        'effectiveFrom' => null,
    ];

    /**
     * Re-usable use statement info array for the "no statements in effect" case.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, array<string, string>>
     */
    protected static $noStatements = [
        'name'     => [],
        'function' => [],
        'const'    => [],
    ];

    /**
     * Test that the track() method sets the class properties correctly.
     *
     * @dataProvider dataTrackSetsProperties
     *
     * @phpcs:disable Generic.Files.LineLength.TooLong
     *
     * @param string                                                                                    $marker   The comment which prefaces the
     *                                                                                                            test target token in the
     *                                                                                                            test file.
     * @param int|string                                                                                $target   The token constant to search
     *                                                                                                            for to find the target token.
     * @param array<string, array<string, string|int|array<string, string|int|array<int, array<int>>>>> $expected The expected property values
     *                                                                                                            at that point in the file.
     *
     * @phpcs:enable Generic.Files.LineLength.TooLong
     *
     * @return void
     */
    public function testTrackSetsProperties($marker, $target, $expected)
    {
        $tracker        = ImportUseTracker::getInstance();
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

        // Test that the tracker has tracked the use statements correctly.
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
            $expected['seenInFile'],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is as expected'
        );

        // Until the `getUseStatementsInfo()` method has been called, the seenInFileResolved property should always be empty.
        $this->assertPropertySame(
            [],
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property is an empty array'
        );
    }

    /**
     * Data provider.
     *
     * @see testTrackSetsProperties() For the array format.
     *
     * @return array<string, array<string, string|int|array<string, string|int|array<int, array<int>>>>>
     */
    abstract public static function dataTrackSetsProperties();

    /**
     * Test retrieving the use statements for a token before the last seen token.
     *
     * @dataProvider dataGetUseStatements
     *
     * @param string                                                                   $marker   The comment which prefaces
     *                                                                                           the test target token.
     * @param int|string                                                               $target   The token constant to
     *                                                                                           search for.
     * @param array<int, array<string, int|array<string, array<string, string>>|null>> $expected The expected value for the
     *                                                                                           seenInFileResolved property.
     *
     * @return void
     */
    public function testUseStatementsForTokenBeforeLastSeen($marker, $target, $expected)
    {
        $tracker        = ImportUseTracker::getInstance();
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

        $this->assertSame(\current($expected)['statements'], $tracker->getUseStatements(self::$phpcsFile, $stackPtr));
    }

    /**
     * Test retrieving the use statements for the "current" stackPtr.
     *
     * @dataProvider dataGetUseStatements
     *
     * @param string                                                                   $marker   The comment which prefaces
     *                                                                                           the test target token.
     * @param int|string                                                               $target   The token constant to
     *                                                                                           search for.
     * @param array<int, array<string, int|array<string, array<string, string>>|null>> $expected The expected value for the
     *                                                                                           seenInFileResolved property.
     *
     * @return void
     */
    public function testGetUseStatementsInfoForCurrentToken($marker, $target, $expected)
    {
        $tracker        = ImportUseTracker::getInstance();
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

        $this->assertSame(
            \current($expected),
            $tracker->getUseStatementsInfo(self::$phpcsFile, $stackPtr),
            'Failed asserting that the returned use statements info array matched expectations'
        );

        $this->assertPropertySame(
            $expected,
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property has stored the result correctly'
        );
    }

    /**
     * Data provider.
     *
     * @see testGetUseStatementsForTokenBeforeLastSeen() For the array format.
     * @see testGetUseStatementsForCurrentToken()        For the array format.
     *
     * @return array<string, array<string, string|int|array<int, array<string, int|array<string, array<string, string>>|null>>>>
     */
    abstract public static function dataGetUseStatements();

    /**
     * Test retrieving the use statements for a token *after* the last seen token (and some before).
     *
     * @dataProvider dataGetUseStatementsArbitraryToken
     *
     * @param string                               $marker       The comment which prefaces the test target token
     *                                                           in the test file.
     * @param int|string                           $target       The token constant to search for to find the target token.
     * @param array<string, array<string, string>> $expected     The expected effective use statements at that point
     *                                                           in the file.
     * @param string                               $stopAt       Comment prefacing a token to stop the tracking at.
     * @param int|string                           $stopAtTarget The token constant for the "stop at" token.
     *
     * @return void
     */
    public function testGetUseStatementsArbitraryToken($marker, $target, $expected, $stopAt, $stopAtTarget)
    {
        $tracker        = ImportUseTracker::getInstance();
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
        $this->assertSame($expected, $tracker->getUseStatements(self::$phpcsFile, $stackPtr));
    }

    /**
     * Data provider.
     *
     * @see testGetUseStatementsArbitraryToken() For the array format.
     *
     * @return array<string, array<string, string|int|array<string, array<string, string>>>>
     */
    abstract public static function dataGetUseStatementsArbitraryToken();
}
