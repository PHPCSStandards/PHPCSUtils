<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2025 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\TestUtils\RulesetDouble;

use PHPCSUtils\TestUtils\ConfigDouble;
use PHPCSUtils\TestUtils\RulesetDouble;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Tests for the \PHPCSUtils\TestUtils\RulesetDouble class.
 *
 * @covers \PHPCSUtils\TestUtils\RulesetDouble
 *
 * @group testutils
 *
 * @since 1.1.0
 */
final class RulesetDoubleTest extends TestCase
{

    /**
     * Verify that creating a ruleset object while limiting the ruleset to a single valid sniff succeeds.
     *
     * @return void
     */
    public function testRegisteringValidSingleSniff()
    {
        $config = new ConfigDouble();

        // Limit the ruleset to just one sniff.
        $config->sniffs = ['Generic.Files.ByteOrderMark'];

        $ruleset = new RulesetDouble($config);

        $this->assertInstanceOf('\PHP_CodeSniffer\Ruleset', $ruleset);
        $this->assertCount(1, $ruleset->sniffs, 'Ruleset did not register exactly 1 sniff');
    }

    /**
     * Verify that creating a ruleset object while limiting the ruleset to a sniff which doesn't exist,
     * creates a Ruleset object without any sniffs registered and does not throw an exception.
     *
     * @return void
     */
    public function testRegisteringSniffWhichDoesntExist()
    {
        $config = new ConfigDouble();

        // Limit the ruleset to just one sniff.
        $config->sniffs = ['Dummy.Dummy.Dummy'];

        $ruleset = new RulesetDouble($config);

        $this->assertInstanceOf('\PHP_CodeSniffer\Ruleset', $ruleset);
        $this->assertCount(0, $ruleset->sniffs, 'Ruleset has registered a sniff, even though it doesn\'t exist');
    }

    /**
     * Verify that if creating a ruleset object would lead to an exception other than the "no sniffs registered"
     * exception, the exception will still be thrown.
     *
     * @return void
     */
    public function testOtherExceptionsAreRethrown()
    {
        $message  = 'ERROR: Referenced sniff "./MissingFile.xml" does not exist.' . \PHP_EOL;
        $message .= 'ERROR: No sniffs were registered.' . \PHP_EOL . \PHP_EOL;

        $exception = 'PHP_CodeSniffer\Exceptions\RuntimeException';

        $this->expectException($exception);
        $this->expectExceptionMessage($message);

        $standard = __DIR__ . '/InvalidSniffRef.xml';
        $config   = new ConfigDouble(["--standard=$standard"]);
        new RulesetDouble($config);
    }
}
