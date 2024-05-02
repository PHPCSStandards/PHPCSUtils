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
 * Tests for the \PHPCSUtils\ContextTracking\NamespaceTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\NamespaceTracker
 *
 * @since 1.1.0
 */
final class LastSeenPtrTest extends UtilityMethodTestCase
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
        self::$caseFile = __DIR__ . '/MultipleUnscopedNamespacesTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Safeguard that a call to track() with a non-integer stack pointer does not change the lastSeenPtr.
     *
     * @return void
     */
    public function testNonIntegerStackPtrGetsIgnored()
    {
        $tracker = NamespaceTracker::getInstance();

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
        $tracker = NamespaceTracker::getInstance();

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
     * Safeguard that tracking after the file has already been (partially) backfilled does not set
     * the lastSeenPtr pointer back.
     *
     * @dataProvider dataLastSeenPtrWillNotBeLowered
     *
     * @param string     $marker         The comment which prefaces the test target token in the test file.
     * @param int|string $target         The token constant to search for to find the target token.
     * @param string     $expectedName   The expected namespace name at that point in the file.
     * @param int        $lastSeenOffset Offset from the stackPtr for the expected "lastSeenPtr".
     *
     * @return void
     */
    public function testLastSeenPtrWillNotBeLowered($marker, $target, $expectedName, $lastSeenOffset)
    {
        $tracker = NamespaceTracker::getInstance();

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        $stackPtr            = $this->getTargetToken($marker, $target);
        $expectedLastSeenPtr = ($stackPtr + $lastSeenOffset);

        // Trigger the backfill.
        $this->assertSame($expectedName, $tracker->getNamespace(self::$phpcsFile, $stackPtr));

        $this->assertPropertySame(
            $expectedLastSeenPtr,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is as expected after calling getNamespace()'
        );

        // Start tracking the file.
        $tracker->track(self::$phpcsFile, 0);

        $this->assertPropertySame(
            $expectedLastSeenPtr,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is still correct when tracking is triggered after backfill'
        );
    }

    /**
     * Data provider.
     *
     * @see testLastSeenPtrWillNotBeLowered() For the array format.
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataLastSeenPtrWillNotBeLowered()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'Inside class: backfill will set lastSeen at end of class' => [
                'marker'         => '/* testInClassBeforeFirstTrackingToken */',
                'target'         => \T_PUBLIC,
                'expectedName'   => 'Vendor\Package\FirstNamespace',
                'lastSeenOffset' => ($php8Names === true) ? 30 : 32,
            ],
            'Function declaration: backfill will set lastSeen at end of function' => [
                'marker'         => '/* testNamespaceDeclarationB */',
                'target'         => \T_FUNCTION,
                'expectedName'   => 'SecondNamespace',
                'lastSeenOffset' => ($php8Names === true) ? 32 : 34,
            ],
        ];
    }
}
