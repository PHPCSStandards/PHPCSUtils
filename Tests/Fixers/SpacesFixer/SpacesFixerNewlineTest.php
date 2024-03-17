<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Fixers\SpacesFixer;

use PHPCSUtils\Tests\Fixers\SpacesFixer\SpacesFixerTestCase;

/**
 * Tests for the \PHPCSUtils\Fixers\SpacesFixer::checkAndFix() method.
 *
 * @covers \PHPCSUtils\Fixers\SpacesFixer::checkAndFix
 *
 * @since 1.0.0
 */
final class SpacesFixerNewlineTest extends SpacesFixerTestCase
{

    /**
     * Expected number of spaces to use for these tests.
     *
     * @var int|string
     */
    const SPACES = 'newline';

    /**
     * The expected replacement for the first placeholder.
     *
     * @var string
     */
    const MSG_REPLACEMENT_1 = 'a new line';

    /**
     * Dummy metric name to use for the test.
     *
     * @var string
     */
    const METRIC = 'testing';

    /**
     * The names of the test case(s) in compliance.
     *
     * @var array<string>
     */
    protected static $compliantCases = [
        'newline-and-trailing-spaces',
        'multiple-newlines-and-spaces',
        'comment-and-new line',
    ];

    /**
     * Full path to the fixed version of the test case file associated with this test class.
     *
     * @var string
     */
    protected static $fixedFile = '/SpacesFixerNewlineTest.inc.fixed';
}
