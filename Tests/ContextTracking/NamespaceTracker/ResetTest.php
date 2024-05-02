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
use Yoast\PHPUnitPolyfills\Polyfills\AssertIsType;

/**
 * Test for the \PHPCSUtils\ContextTracking\NamespaceTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\NamespaceTracker
 *
 * @since 1.1.0
 */
final class ResetTest extends UtilityMethodTestCase
{
    use AssertIsType;
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
        self::$caseFile = \str_replace('.php', '.1.inc', __FILE__);
        parent::setUpTestFile();
    }

    /**
     * Test that the track() method resets the class properties correctly when passed a different file
     * containing a namespace declaration.
     *
     * @return void
     */
    public function testPropertiesResetOnNamespaceDeclarationInNextFile()
    {
        $tracker = NamespaceTracker::getInstance();
        $targets = $tracker->getTargetTokens();

        $tokens = self::$phpcsFile->getTokens();

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($targets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // Test that the tracker has tracked the namespace of the first file correctly.
        $this->verifyPropertiesAfterProcessingFirstFile();

        // Get a second file to process.
        $sharedRuleSet  = self::$phpcsFile->ruleset;
        $sharedConfig   = self::$phpcsFile->config;
        $secondCaseFile = \str_replace('.php', '.2.inc', __FILE__);

        $secondFile = self::parseFile($secondCaseFile, $sharedRuleSet, $sharedConfig);

        $tokens = $secondFile->getTokens();

        for ($i = 0; $i < $secondFile->numTokens; $i++) {
            if ($i === 0 || isset($targets[$tokens[$i]['code']]) === true) {
                $tracker->track($secondFile, $i);
            }

            if ($i === 0) {
                // Test that the tracker has reset correctly on encountering the second file.
                $this->verifyPropertiesHaveReset($secondCaseFile);
            }
        }

        $php8Names = parent::usesPhp8NameTokens();

        // Test that the tracker has tracked the namespace of the second file correctly.
        $this->assertPropertySame(
            $secondCaseFile,
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is correct after processing the second file'
        );
        $this->assertPropertySame(
            4,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is correct after processing the second file'
        );
        $this->assertPropertySame(
            1,
            'currentNamespacePtr',
            $tracker,
            'Failed asserting that the currentNamespacePtr property is correct after processing the second file'
        );
        $this->assertPropertySame(
            [
                0 => [
                    'start' => 0,
                    'end'   => ($php8Names === true) ? 7 : 9,
                    'name'  => '',
                ],
                1 => [
                    'start' => ($php8Names === true) ? 8 : 10,
                    'end'   => null,
                    'name'  => 'Bar\Foo',
                ],

            ],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is correct after processing the second file'
        );
    }

    /**
     * Test that the getNamespace() method resets the class properties correctly when passed a different file
     * than the last one tracked.
     *
     * @dataProvider dataPropertiesResetOnGetNamespaceFromDifferentFile
     *
     * @param string $fileSuffix File suffix for the secondary test case file.
     * @param string $expected   Expected namespace name.
     *
     * @return void
     */
    public function testPropertiesResetOnGetNamespaceFromDifferentFile($fileSuffix, $expected)
    {
        $tracker = NamespaceTracker::getInstance();
        $targets = $tracker->getTargetTokens();

        $tokens = self::$phpcsFile->getTokens();

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($targets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // Test that the tracker has tracked the namespace of the first file correctly.
        $this->verifyPropertiesAfterProcessingFirstFile();

        // Get a second file to process.
        $sharedRuleSet  = self::$phpcsFile->ruleset;
        $sharedConfig   = self::$phpcsFile->config;
        $secondCaseFile = \str_replace('.php', $fileSuffix, __FILE__);

        $secondFile = self::parseFile($secondCaseFile, $sharedRuleSet, $sharedConfig);

        $tokens = $secondFile->getTokens();

        // Explicitly not tracking the second file to force the `getNamespace()` method to initialize the tracking.

        $stackPtr = $secondFile->findNext(\T_ECHO, 0);
        $this->assertIsInt($stackPtr, 'Echo token could not be found');

        $this->assertSame(
            $expected,
            $tracker->getNamespace($secondFile, $stackPtr),
            'Namespace in "next" file was not correctly identified'
        );
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataPropertiesResetOnGetNamespaceFromDifferentFile()
    {
        return [
            'Next file contains namespace declaration' => [
                'fileSuffix' => '.2.inc',
                'expected'   => 'Bar\Foo',
            ],
            'Next file does not contain namespace declaration, but does contain namespace operator' => [
                'fileSuffix' => '.3.inc',
                'expected'   => '',
            ],
            'Next file does not contain namespace keyword' => [
                'fileSuffix' => '.4.inc',
                'expected'   => '',
            ],
        ];
    }

    /**
     * Helper method to verify the class properties are set after processing the first file.
     *
     * @return void
     */
    private function verifyPropertiesAfterProcessingFirstFile()
    {
        $tracker   = NamespaceTracker::getInstance();
        $php8Names = parent::usesPhp8NameTokens();

        // Test that the tracker has tracked the namespace of the first file correctly.
        $this->assertPropertySame(
            self::$caseFile,
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is correct after processing the first file'
        );
        $this->assertPropertySame(
            2,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is correct after processing the first file'
        );
        $this->assertPropertySame(
            1,
            'currentNamespacePtr',
            $tracker,
            'Failed asserting that the currentNamespacePtr property is correct after processing the first file'
        );
        $this->assertPropertySame(
            [
                0 => [
                    'start' => 0,
                    'end'   => ($php8Names === true) ? 5 : 7,
                    'name'  => '',
                ],
                1 => [
                    'start' => ($php8Names === true) ? 6 : 8,
                    'end'   => null,
                    'name'  => 'Foo\Bar',
                ],

            ],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is correct after processing the first file'
        );
    }

    /**
     * Helper method to verify the class properties have been reset to their defaults.
     *
     * @param string $pathToFile Path to the file currently being processed.
     *
     * @return void
     */
    private function verifyPropertiesHaveReset($pathToFile)
    {
        $tracker = NamespaceTracker::getInstance();

        $this->assertPropertySame(
            $pathToFile,
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property was correctly reset on seeing a new file'
        );
        $this->assertPropertySame(
            0,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is 0 after reset on seeing a new file'
        );
        $this->assertPropertySame(
            0,
            'currentNamespacePtr',
            $tracker,
            'Failed asserting that the currentNamespacePtr property is 0 after reset on seeing a new file'
        );
        $this->assertPropertySame(
            [
                0 => [
                    'start' => 0,
                    'end'   => null,
                    'name'  => '',
                ],
            ],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is as expected after reset on seeing a new file'
        );
    }
}
