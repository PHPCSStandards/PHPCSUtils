<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\TestUtils\UtilityMethodTestCase;

use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Tests for the \PHPCSUtils\TestUtils\UtilityMethodTestCase class.
 *
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::testTestMarkersAreUnique
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::assertTestMarkersAreUnique
 *
 * @since 1.1.0
 */
final class TestMarkersAreUniqueNoMarkersTest extends PolyfilledTestCase
{

    /**
     * Overload the "normal" test marker QA check, but only to overload the `@covers` tags.
     *
     * @return void
     */
    public function testTestMarkersAreUnique()
    {
        parent::testTestMarkersAreUnique();
    }
}
