<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2021 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\MessageHelper;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\MessageHelper;

/**
 * Tests for the \PHPCSUtils\Utils\MessageHelper::addMessage() and the
 * \PHPCSUtils\Utils\MessageHelper::addFixableMessage() methods.
 *
 * {@internal Note: this is largely testing PHPCS native functionality, but as PHPCS doesn't
 * have any unit tests in place for this functionality, that's not a bad thing.}
 *
 * @group messagehelper
 *
 * @since 1.0.0
 */
class AddMessageTest extends UtilityMethodTestCase
{

    /**
     * Dummy error code to use for the test.
     *
     * Using the dummy full error code to force it to record.
     *
     * @var string
     */
    const CODE = 'PHPCSUtils.MessageHelper.AddMessageTest.Found';

    /**
     * Set the name of a sniff to pass to PHPCS to limit the run (and force it to record errors).
     *
     * @var array
     */
    protected static $selectedSniff = ['PHPCSUtils.MessageHelper.AddMessageTest'];

    /**
     * Test the addMessage wrapper.
     *
     * @dataProvider dataAddMessage
     * @covers       \PHPCSUtils\Utils\MessageHelper::addMessage
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $isError    Whether to test adding an error or a warning.
     * @param array  $expected   Expected error details.
     *
     * @return void
     */
    public function testAddMessage($testMarker, $isError, $expected)
    {
        $tokens               = self::$phpcsFile->getTokens();
        $stackPtr             = $this->getTargetToken($testMarker, \T_CONSTANT_ENCAPSED_STRING);
        $severity             = \mt_rand(5, 10);
        $expected['severity'] = $severity;

        $return = MessageHelper::addMessage(
            self::$phpcsFile,
            'Message added. Text: %s',
            $stackPtr,
            $isError,
            static::CODE,
            [$tokens[$stackPtr]['content']],
            $severity
        );

        $this->assertTrue($return);

        $this->verifyRecordedMessages($stackPtr, $isError, $expected);
    }

    /**
     * Data Provider.
     *
     * @see testAddMessage() For the array format.
     *
     * @return array
     */
    public function dataAddMessage()
    {
        return [
            'add-error' => [
                '/* testAddErrorMessage */',
                true,
                [
                    'message' => "Message added. Text: 'test 1'",
                    'source'  => static::CODE,
                    'fixable' => false,
                ],
            ],
            'add-warning' => [
                '/* testAddWarningMessage */',
                false,
                [
                    'message' => "Message added. Text: 'test 2'",
                    'source'  => static::CODE,
                    'fixable' => false,
                ],
            ],
        ];
    }

    /**
     * Test the addFixableMessage wrapper.
     *
     * @dataProvider dataAddFixableMessage
     * @covers       \PHPCSUtils\Utils\MessageHelper::addFixableMessage
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $isError    Whether to test adding an error or a warning.
     * @param array  $expected   Expected error details.
     *
     * @return void
     */
    public function testAddFixableMessage($testMarker, $isError, $expected)
    {
        $tokens               = self::$phpcsFile->getTokens();
        $stackPtr             = $this->getTargetToken($testMarker, \T_CONSTANT_ENCAPSED_STRING);
        $severity             = \mt_rand(5, 10);
        $expected['severity'] = $severity;

        $return = MessageHelper::addFixableMessage(
            self::$phpcsFile,
            'Message added. Text: %s',
            $stackPtr,
            $isError,
            static::CODE,
            [$tokens[$stackPtr]['content']],
            $severity
        );

        // Fixable message recording only returns true when the fixer is enabled (=phpcbf).
        $this->assertFalse($return);

        $this->verifyRecordedMessages($stackPtr, $isError, $expected);
    }

    /**
     * Data Provider.
     *
     * @see testAddFixableMessage() For the array format.
     *
     * @return array
     */
    public function dataAddFixableMessage()
    {
        return [
            'add-fixable-error' => [
                '/* testAddFixableErrorMessage */',
                true,
                [
                    'message' => "Message added. Text: 'test 3'",
                    'source'  => static::CODE,
                    'fixable' => true,
                ],
            ],
            'add-fixable-warning' => [
                '/* testAddFixableWarningMessage */',
                false,
                [
                    'message' => "Message added. Text: 'test 4'",
                    'source'  => static::CODE,
                    'fixable' => true,
                ],
            ],
        ];
    }

    /**
     * Helper method to verify the expected message details.
     *
     * @param int   $stackPtr The stack pointer on which the error/warning is expected.
     * @param bool  $isError  Whether to test adding an error or a warning.
     * @param array $expected Expected error details.
     *
     * @return void
     */
    protected function verifyRecordedMessages($stackPtr, $isError, $expected)
    {
        $tokens   = self::$phpcsFile->getTokens();
        $errors   = self::$phpcsFile->getErrors();
        $warnings = self::$phpcsFile->getWarnings();
        $result   = ($isError === true) ? $errors : $warnings;

        /*
         * Make sure that no errors/warnings were recorded when the other type is set to be expected.
         */
        if ($isError === true) {
            $this->assertArrayNotHasKey(
                $tokens[$stackPtr]['line'],
                $warnings,
                'Expected no warnings on line ' . $tokens[$stackPtr]['line'] . '. At least one found.'
            );
        } else {
            $this->assertArrayNotHasKey(
                $tokens[$stackPtr]['line'],
                $errors,
                'Expected no errors on line ' . $tokens[$stackPtr]['line'] . '. At least one found.'
            );
        }

        /*
         * Make sure the expected array keys for the errors/warnings are available.
         */
        $this->assertArrayHasKey(
            $tokens[$stackPtr]['line'],
            $result,
            'Expected a violation on line ' . $tokens[$stackPtr]['line'] . '. None found.'
        );

        $this->assertArrayHasKey(
            $tokens[$stackPtr]['column'],
            $result[$tokens[$stackPtr]['line']],
            'Expected a violation on line ' . $tokens[$stackPtr]['line'] . ', column '
                . $tokens[$stackPtr]['column'] . '. None found.'
        );

        $messages = $result[$tokens[$stackPtr]['line']][$tokens[$stackPtr]['column']];

        // Expect one violation.
        $this->assertCount(1, $messages, 'Expected 1 violation, found: ' . \count($messages));

        $violation = $messages[0];

        // PHPCS 2.x places `unknownSniff.` before the actual error code for utility tests with a dummy error code.
        $violation['source'] = \str_replace('unknownSniff.', '', $violation['source']);

        /*
         * Test the violation details.
         */
        foreach ($expected as $key => $value) {
            $this->assertSame($value, $violation[$key], \ucfirst($key) . ' comparison failed');
        }
    }
}
