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
 * Test for the \PHPCSUtils\ContextTracking\DeclaredFunctionsTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\DeclaredFunctionsTracker
 *
 * @since 1.1.0
 */
final class ResetTest extends UtilityMethodTestCase
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
        self::$caseFile = \str_replace('.php', '.1.inc', __FILE__);
        parent::setUpTestFile();
    }

    /**
     * Test that the track() method resets the class properties correctly when passed a different file.
     *
     * @return void
     */
    public function testPropertiesResetOnNextFile()
    {
        $tracker = DeclaredFunctionsTracker::getInstance();
        $targets = $tracker->getTargetTokens();

        $tokens = self::$phpcsFile->getTokens();

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($targets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // Make sure the seenInFileResolved property will also be set.
        $tracker->getFunctions(self::$phpcsFile);

        // Test that the tracker has tracked the functions in the first file correctly.
        $this->verifyPropertiesAfterProcessingFirstFile();

        // Get a second file to process.
        $sharedRuleSet  = self::$phpcsFile->ruleset;
        $sharedConfig   = self::$phpcsFile->config;
        $secondCaseFile = \str_replace('.php', '.3.inc', __FILE__);

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

        $php8Names          = parent::usesPhp8NameTokens();
        $functionTokenFile2 = ($php8Names === true) ? 11 : 15;

        // Test that the tracker has tracked the functions in the second file correctly.
        $this->assertPropertySame(
            $secondCaseFile,
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is correct after processing the second file'
        );
        $this->assertPropertySame(
            $functionTokenFile2,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is correct after processing the second file'
        );
        $this->assertPropertySame(
            [$functionTokenFile2],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is correct after processing the second file'
        );

        // Verify the seenInFileResolved property has not been set again.
        $this->assertPropertySame(
            null,
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property is correct after processing the second file'
        );
    }

    /**
     * Test that the track() method resets the class properties correctly when passed a different file
     * containing a namespace keyword.
     *
     * @return void
     */
    public function testPropertiesResetOnNamespaceKeywordInNextFile()
    {
        if (parent::usesPhp8NameTokens() === true) {
            // Namespace operator is not tokenized as T_NAMESPACE in PHPCS 4.x.
            $this->markTestSkipped('Test will only work in combination with PHPCS 3.x');
        }

        $tracker = DeclaredFunctionsTracker::getInstance();
        $targets = $tracker->getTargetTokens();

        $tokens = self::$phpcsFile->getTokens();

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($targets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // Make sure the seenInFileResolved property will also be set.
        $tracker->getFunctions(self::$phpcsFile);

        // Test that the tracker has tracked the functions in the first file correctly.
        $this->verifyPropertiesAfterProcessingFirstFile();

        // Get a second file to process.
        $sharedRuleSet  = self::$phpcsFile->ruleset;
        $sharedConfig   = self::$phpcsFile->config;
        $secondCaseFile = \str_replace('.php', '.8.inc', __FILE__);

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

        // Test that the tracker has tracked the functions in the second file correctly.
        $this->assertPropertySame(
            $secondCaseFile,
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is correct after processing the second file'
        );
        $this->assertPropertySame(
            7,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is correct after processing the second file'
        );
        $this->assertPropertySame(
            [],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is correct after processing the second file'
        );

        // Verify the seenInFileResolved property has not been set again.
        $this->assertPropertySame(
            null,
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property is correct after processing the second file'
        );
    }

    /**
     * Test that the track() method resets the class properties correctly when passed a different file
     * containing a function declaration.
     *
     * @return void
     */
    public function testPropertiesResetOnFunctionKeywordInNextFile()
    {
        $tracker = DeclaredFunctionsTracker::getInstance();
        $targets = $tracker->getTargetTokens();

        $tokens = self::$phpcsFile->getTokens();

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($targets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // Make sure the seenInFileResolved property will also be set.
        $tracker->getFunctions(self::$phpcsFile);

        // Test that the tracker has tracked the functions in the first file correctly.
        $this->verifyPropertiesAfterProcessingFirstFile();

        // Get a second file to process.
        $sharedRuleSet  = self::$phpcsFile->ruleset;
        $sharedConfig   = self::$phpcsFile->config;
        $secondCaseFile = \str_replace('.php', '.5.inc', __FILE__);

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

        $php8Names          = parent::usesPhp8NameTokens();
        $functionTokenFile2 = ($php8Names === true) ? 11 : 13;

        // Test that the tracker has tracked the functionss in the second file correctly.
        $this->assertPropertySame(
            $secondCaseFile,
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is correct after processing the second file'
        );
        $this->assertPropertySame(
            $functionTokenFile2,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is correct after processing the second file'
        );
        $this->assertPropertySame(
            [$functionTokenFile2],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is correct after processing the second file'
        );

        // Verify the seenInFileResolved property has not been set again.
        $this->assertPropertySame(
            null,
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property is correct after processing the second file'
        );
    }

    /**
     * Test that the getFunctions() method resets the class properties correctly when passed a different file
     * than the last one tracked.
     *
     * @dataProvider dataPropertiesResetOnGetFunctionsFromDifferentFile
     *
     * @param string             $fileSuffix File suffix for the secondary test case file.
     * @param array<string, int> $expected   Expected functions.
     *
     * @return void
     */
    public function testPropertiesResetOnGetFunctionsFromDifferentFile($fileSuffix, $expected)
    {
        $tracker = DeclaredFunctionsTracker::getInstance();
        $targets = $tracker->getTargetTokens();

        $tokens = self::$phpcsFile->getTokens();

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($targets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // Test that the tracker has tracked the functions of the first file correctly.
        $this->verifyPropertiesAfterProcessingFirstFile(false);

        // Get a second file to process.
        $sharedRuleSet  = self::$phpcsFile->ruleset;
        $sharedConfig   = self::$phpcsFile->config;
        $secondCaseFile = \str_replace('.php', $fileSuffix, __FILE__);

        $secondFile = self::parseFile($secondCaseFile, $sharedRuleSet, $sharedConfig);

        $tokens = $secondFile->getTokens();

        // Explicitly not tracking the second file to force the `getFunctions()` method to initialize the tracking.

        $this->assertSame(
            $expected,
            $tracker->getFunctions($secondFile),
            'Declared functions list from next file did not match expectation'
        );

        // Test that the tracker has tracked the functions in the second file correctly.
        $this->assertPropertySame(
            $secondCaseFile,
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is correct after processing the second file'
        );
        $this->assertPropertySame(
            ($secondFile->numTokens - 1),
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is correct after processing the second file'
        );
        $this->assertPropertySame(
            $expected,
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property is correct after processing the second file'
        );
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, string|array<string, int>>>
     */
    public static function dataPropertiesResetOnGetFunctionsFromDifferentFile()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'Next file contains a different namespace, no functions' => [
                'fileSuffix' => '.2.inc',
                'expected'   => [],
            ],
            'Next file contains a different namespace, has function' => [
                'fileSuffix' => '.3.inc',
                'expected'   => [
                    '\Count\Me\In\countMeIn' => ($php8Names === true) ? 11 : 15,
                ],
            ],
            'Next file contains same namespace, no functions' => [
                'fileSuffix' => '.4.inc',
                'expected'   => [],
            ],
            'Next file contains same namespace, has different function' => [
                'fileSuffix' => '.5.inc',
                'expected'   => [
                    '\Foo\Bar\alsoNamespaced' => ($php8Names === true) ? 11 : 13,
                ],
            ],
            'Next file contains no namespace, no functions' => [
                'fileSuffix' => '.6.inc',
                'expected'   => [],
            ],
            'Next file contains no namespace, has function' => [
                'fileSuffix' => '.7.inc',
                'expected'   => [
                    '\countMe' => 5,
                ],
            ],
            'Next file contains no namespace, no functions, but has namespace keyword' => [
                'fileSuffix' => '.8.inc',
                'expected'   => [],
            ],
        ];
    }

    /**
     * Helper method to verify the class properties are set after processing the first file.
     *
     * @param bool $checkResolved Whether to also check the $seenInFileResolved property.
     *
     * @return void
     */
    private function verifyPropertiesAfterProcessingFirstFile($checkResolved = true)
    {
        $tracker       = DeclaredFunctionsTracker::getInstance();
        $functionToken = $this->getTargetToken('/* functionMarker */', \T_FUNCTION);

        // Test that the tracker has tracked the function declarations correctly.
        $this->assertPropertySame(
            self::$caseFile,
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is correct after processing the first file'
        );
        $this->assertPropertySame(
            ($checkResolved === true) ? (self::$phpcsFile->numTokens - 1) : $functionToken,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is correct after processing the first file'
        );
        $this->assertPropertySame(
            [$functionToken],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is correct after processing the first file'
        );

        if ($checkResolved === true) {
            // Verify the seenInFileResolved property is set correctly after the `getFunctions()` method has been called.
            $this->assertPropertySame(
                ['\Foo\Bar\namespaced' => $functionToken],
                'seenInFileResolved',
                $tracker,
                'Failed asserting that the seenInFileResolved property is correct after processing the first file'
            );
        }
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
        $tracker = DeclaredFunctionsTracker::getInstance();

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
            'Failed asserting that the lastSeenPtr property was correctly reset on seeing a new file'
        );
        $this->assertPropertySame(
            [],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property was correctly reset on seeing a new file'
        );
        $this->assertPropertySame(
            null,
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property was correctly reset on seeing a new file'
        );
    }
}
