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
abstract class GetFunctionsTestCase extends UtilityMethodTestCase
{
    use AssertPropertySame;

    /**
     * List of all the function markers in the test case file and their FQN function name.
     *
     * Should be declared in the concrete test class.
     *
     * @var array<string, string>
     */
    protected $functionMarkers = [];

    /**
     * Test retrieving the functions declared in a file.
     *
     * @return void
     */
    public function testGetFunctions()
    {
        // Create the expectations array.
        $expected = [];
        foreach ($this->functionMarkers as $marker => $name) {
            $expected[$name] = $this->getTargetToken($marker, [\T_FUNCTION]);
        }

        $tracker         = DeclaredFunctionsTracker::getInstance();
        $trackerTargets  = $tracker->getTargetTokens();
        $trackerTargets += [
            \T_START_HEREDOC        => \T_START_HEREDOC,
            \T_START_NOWDOC         => \T_START_NOWDOC,
            \T_DOC_COMMENT_OPEN_TAG => \T_DOC_COMMENT_OPEN_TAG,
            \T_ATTRIBUTE            => \T_ATTRIBUTE,
        ];

        // Reset the singleton to allow for testing each test case in isolation.
        $tracker->reset();

        $tokens = self::$phpcsFile->getTokens();

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset($trackerTargets[$tokens[$i]['code']]) === true) {
                $tracker->track(self::$phpcsFile, $i);
            }
        }

        $this->assertSame(
            $expected,
            $tracker->getFunctions(self::$phpcsFile),
            'Retrieved functions list does not comply with expectations'
        );

        $this->assertSame(
            $expected,
            $this->getObjectPropertyValue($tracker, 'seenInFileResolved'),
            'The retrieved functions list was not cached correctly to seenInFileResolved'
        );

        // This second call is to test the logic to re-use a previously stored result.
        $this->assertSame(
            $expected,
            $tracker->getFunctions(self::$phpcsFile),
            'Retrieved functions list does not comply with expectations when called a second time'
        );
    }

    /**
     * Test that the findInFile() method finds functions correctly in the file.
     *
     * @dataProvider dataFindInFile
     *
     * @param string       $input    The name of the function to find.
     * @param string|false $expected A marker comment for the function which should be found or FALSE
     *                               if the expectation is for the function not to be found in the file.
     *
     * @return void
     */
    public function testFindInFile($input, $expected)
    {
        if (\is_string($expected)) {
            $expected = $this->getTargetToken($expected, [\T_FUNCTION]);
        }

        $tracker = DeclaredFunctionsTracker::getInstance();
        $this->assertSame($expected, $tracker->findInFile(self::$phpcsFile, $input));
    }

    /**
     * Data provider.
     *
     * @see testFindInFile() For the array format.
     *
     * @return array<string, array<string, string|false>>
     */
    abstract public static function dataFindInFile();
}
