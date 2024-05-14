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
 * Test for the \PHPCSUtils\ContextTracking\ImportUseTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\ImportUseTracker
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
     * Test that the track() method resets the class properties correctly when passed a different file
     * containing a namespace declaration.
     *
     * @return void
     */
    public function testPropertiesResetOnNamespaceDeclarationInNextFile()
    {
        $tracker = ImportUseTracker::getInstance();
        $targets = $tracker->getTargetTokens();

        $tokens = self::$phpcsFile->getTokens();

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($targets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // Make sure the seenInFileResolved property will also be set.
        $stackPtr = $this->getTargetToken('/* testFirstNamespace */', \T_NEW);
        $tracker->getUseStatements(self::$phpcsFile, $stackPtr);

        $stackPtr = $this->getTargetToken('/* testSecondNamespace */', \T_NEW);
        $tracker->getUseStatements(self::$phpcsFile, $stackPtr);

        // Test that the tracker has tracked the use statements in the first file correctly.
        $this->verifyPropertiesAfterProcessingFirstFile($stackPtr);

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

        // Test that the tracker has tracked the use statements in the second file correctly.
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
            [],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is correct after processing the second file'
        );

        // Verify the seenInFileResolved property has not been set again.
        $this->assertPropertySame(
            [],
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property is correct after processing the second file'
        );
    }

    /**
     * Test that the track() method resets the class properties correctly when passed a different file
     * containing an import use statement.
     *
     * @return void
     */
    public function testPropertiesResetOnImportUseInNextFile()
    {
        $tracker = ImportUseTracker::getInstance();
        $targets = $tracker->getTargetTokens();

        $tokens = self::$phpcsFile->getTokens();

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($targets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // Make sure the seenInFileResolved property will also be set.
        $stackPtr = $this->getTargetToken('/* testFirstNamespace */', \T_NEW);
        $tracker->getUseStatements(self::$phpcsFile, $stackPtr);

        $stackPtr = $this->getTargetToken('/* testSecondNamespace */', \T_NEW);
        $tracker->getUseStatements(self::$phpcsFile, $stackPtr);

        // Test that the tracker has tracked the use statements in the first file correctly.
        $this->verifyPropertiesAfterProcessingFirstFile($stackPtr);

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

        // Test that the tracker has tracked the use statements in the second file correctly.
        $this->assertPropertySame(
            $secondCaseFile,
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is correct after processing the second file'
        );
        $this->assertPropertySame(
            10,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is correct after processing the second file'
        );
        $this->assertPropertySame(
            [
                0 => [5, 10],
            ],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is correct after processing the second file'
        );

        // Verify the seenInFileResolved property has not been set again.
        $this->assertPropertySame(
            [],
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property is correct after processing the second file'
        );
    }

    /**
     * Test that the getUseStatements() method resets the class properties correctly when passed a different file
     * than the last one tracked.
     *
     * @dataProvider dataPropertiesResetOnGetUseStatementsFromDifferentFile
     *
     * @param string                               $fileSuffix File suffix for the secondary test case file.
     * @param array<string, array<string, string>> $expected   Expected import use statements.
     *
     * @return void
     */
    public function testPropertiesResetOnGetUseStatementsFromDifferentFile($fileSuffix, $expected)
    {
        $tracker = ImportUseTracker::getInstance();
        $targets = $tracker->getTargetTokens();

        $tokens = self::$phpcsFile->getTokens();

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($targets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        // Test that the tracker has tracked the use statements in the first file correctly.
        $stackPtr = $this->getTargetToken('/* testSecondNamespace */', \T_USE);
        $this->verifyPropertiesAfterProcessingFirstFile($stackPtr, false);

        // Get a second file to process.
        $sharedRuleSet  = self::$phpcsFile->ruleset;
        $sharedConfig   = self::$phpcsFile->config;
        $secondCaseFile = \str_replace('.php', $fileSuffix, __FILE__);

        $secondFile = self::parseFile($secondCaseFile, $sharedRuleSet, $sharedConfig);

        $tokens = $secondFile->getTokens();

        // Explicitly not tracking the second file to force the `getUseStatements()` method to initialize the tracking.

        $stackPtr = $secondFile->findNext(\T_NEW, 0);
        $this->assertNotFalse($stackPtr, 'Test token not found');

        $this->assertSame(
            $expected,
            $tracker->getUseStatements($secondFile, $stackPtr),
            'Use statements in next file did not match expectation'
        );
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, string|array<string, array<string, string>>>>
     */
    public static function dataPropertiesResetOnGetUseStatementsFromDifferentFile()
    {
        $noStatements = [
            'name'     => [],
            'function' => [],
            'const'    => [],
        ];

        return [
            'Next file contains namespace declaration, no use statements' => [
                'fileSuffix' => '.2.inc',
                'expected'   => $noStatements,
            ],
            'Next file contains namespace declaration and use statements' => [
                'fileSuffix' => '.3.inc',
                'expected'   => [
                    'name'     => [
                        'Exception' => 'Exception',
                        'ClassName' => 'My\ClassName',
                    ],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'Next file contains no namespace declaration, no use statements' => [
                'fileSuffix' => '.4.inc',
                'expected'   => $noStatements,
            ],
            'Next file contains no namespace declaration, but has use statements' => [
                'fileSuffix' => '.5.inc',
                'expected'   => [
                    'name'     => [
                        'RuntimeException' => 'RuntimeException',
                        'Util'             => 'My\Util',
                    ],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'Next file contains no namespace declaration, no use statements, but has use keyword' => [
                'fileSuffix' => '.6.inc',
                'expected'   => $noStatements,
            ],
        ];
    }

    /**
     * Helper method to verify the class properties are set after processing the first file.
     *
     * @param int  $lastSeenPtr   Expected `$lastSeenPtr` value.
     * @param bool $checkResolved Whether to also check the $seenInFileResolved property.
     *
     * @return void
     */
    private function verifyPropertiesAfterProcessingFirstFile($lastSeenPtr, $checkResolved = true)
    {
        $tracker   = ImportUseTracker::getInstance();
        $php8Names = parent::usesPhp8NameTokens();

        // Test that the tracker has tracked the use statements correctly.
        $this->assertPropertySame(
            self::$caseFile,
            'currentFile',
            $tracker,
            'Failed asserting that the currentFile property is correct after processing the first file'
        );

        $this->assertPropertySame(
            $lastSeenPtr,
            'lastSeenPtr',
            $tracker,
            'Failed asserting that the lastSeenPtr property is correct after processing the first file'
        );

        $this->assertPropertySame(
            [
                ($php8Names === true) ? 8 : 10  => ($php8Names === true) ? [10] : [12],
                ($php8Names === true) ? 28 : 30 => ($php8Names === true) ? [30] : [32],
            ],
            'seenInFile',
            $tracker,
            'Failed asserting that the seenInFile property is correct after processing the first file'
        );

        if ($checkResolved === true) {
            // Verify the seenInFileResolved property is set correctly after the `getUseStatements()` method
            // has been called twice.
            $this->assertPropertySame(
                [
                    ($php8Names === true) ? 8 : 10 => [
                        'lastPtr'       => ($php8Names === true) ? 10 : 12,
                        'statements'    => [
                            'name'     => [
                                'DateTime' => 'DateTime',
                            ],
                            'function' => [],
                            'const'    => [],
                        ],
                        'effectiveFrom' => ($php8Names === true) ? 14 : 16,
                    ],
                    ($php8Names === true) ? 28 : 30 => [
                        'lastPtr'       => ($php8Names === true) ? 30 : 32,
                        'statements'    => [
                            'name'     => [
                                'stdClass' => 'stdClass',
                            ],
                            'function' => [],
                            'const'    => [],
                        ],
                        'effectiveFrom' => ($php8Names === true) ? 34 : 36,
                    ],
                ],
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
        $tracker = ImportUseTracker::getInstance();

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
            [],
            'seenInFileResolved',
            $tracker,
            'Failed asserting that the seenInFileResolved property was correctly reset on seeing a new file'
        );
    }
}
