<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2021 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Xtra\Messages;

use PHP_CodeSniffer\Reporter;
use PHP_CodeSniffer\Reports\Full;
use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Tests for new line handling in PHPCS error/warning messages.
 *
 * {@internal Note: this is testing PHPCS native functionality, but as PHPCS doesn't
 * have any unit tests in place for this functionality, that's not a bad thing.}
 *
 * @coversNothing
 *
 * @group xtra
 *
 * @since 1.0.0
 */
final class HasNewLineSupportTest extends PolyfilledTestCase
{

    /**
     * Dummy error code to use for the test.
     *
     * Using the dummy full error code to force it to record.
     *
     * @var string
     */
    const CODE = 'PHPCSUtils.Xtra.HasNewLineSupportTest.Found';

    /**
     * Set the name of a sniff to pass to PHPCS to limit the run (and force it to record errors).
     *
     * @var array<string>
     */
    protected static $selectedSniff = ['PHPCSUtils.Xtra.HasNewLineSupportTest'];

    /**
     * Test that PHPCS properly supports new lines in error messages.
     *
     * @return void
     */
    public function testHasNewLineSupport()
    {
        /*
         * Set up the expected output.
         * phpcs:disable Generic.Files.LineLength.TooLong
         */
        $expected = <<<'EOD'
------------------------------------------------------------------------------------------------------------------------
FOUND 1 ERROR AFFECTING 1 LINE
------------------------------------------------------------------------------------------------------------------------
 4 | ERROR | Lorem ipsum dolor sit amet, consectetur adipiscing elit.
   |       | Aenean felis urna, dictum vitae lobortis vitae, maximus nec enim. Etiam euismod placerat efficitur. Nulla
   |       | eu felis ipsum.
   |       | Cras vitae ultrices turpis. Ut consectetur ligula in justo tincidunt mattis.
   |       |
   |       | Aliquam fermentum magna id venenatis placerat. Curabitur lobortis nulla sit amet consequat fermentum.
   |       | Aenean malesuada tristique aliquam. Donec eget placerat nisl.
   |       |
   |       | Morbi mollis, risus vel venenatis accumsan, urna dolor faucibus risus, ut congue purus augue vel ipsum.
   |       | Curabitur nec dolor est. Suspendisse nec quam non ligula aliquam tempus. Donec laoreet maximus leo, in
   |       | eleifend odio interdum vitae.
------------------------------------------------------------------------------------------------------------------------
EOD;

        // Make sure space on empty line is included (often removed by file editor).
        $expected = \str_replace("|\n", "| \n", $expected);

        /*
         * Create the error.
         */
        $stackPtr = $this->getTargetToken('/* testMessageWithNewLine */', \T_CONSTANT_ENCAPSED_STRING);

        self::$phpcsFile->addError(
            // phpcs:ignore Generic.Files.LineLength.TooLong
            "Lorem ipsum dolor sit amet, consectetur adipiscing elit.\nAenean felis urna, dictum vitae lobortis vitae, maximus nec enim. Etiam euismod placerat efficitur. Nulla eu felis ipsum.\nCras vitae ultrices turpis. Ut consectetur ligula in justo tincidunt mattis.\n\nAliquam fermentum magna id venenatis placerat. Curabitur lobortis nulla sit amet consequat fermentum. Aenean malesuada tristique aliquam. Donec eget placerat nisl.\n\nMorbi mollis, risus vel venenatis accumsan, urna dolor faucibus risus, ut congue purus augue vel ipsum.\nCurabitur nec dolor est. Suspendisse nec quam non ligula aliquam tempus. Donec laoreet maximus leo, in eleifend odio interdum vitae.",
            $stackPtr,
            self::CODE
        );

        /*
         * Generate the actual output to test.
         */
        $config              = self::$phpcsFile->config;
        $config->colors      = false;
        $config->reportWidth = 120;
        $reporter            = new Reporter($config);
        $reportClass         = new Full();
        $reportData          = $reporter->prepareFileReport(self::$phpcsFile);

        \ob_start();
        $reportClass->generateFileReport(
            $reportData,
            self::$phpcsFile,
            self::$phpcsFile->config->showSources,
            $config->reportWidth
        );
        $output = \ob_get_contents();
        \ob_end_clean();

        $this->assertSame($expected, $this->normalizeOutput($output));
    }

    /**
     * Normalize the output to allow for OS-independent comparison.
     *
     * @param string $output Generated output.
     *
     * @return string
     */
    public function normalizeOutput($output)
    {
        // Remove potential color codes.
        $output = \preg_replace('`\\033\[[0-9]+m`', '', $output);

        $output = \explode(\PHP_EOL, \trim($output));
        // Remove: line with filename.
        \array_shift($output);

        return \implode("\n", $output);
    }
}
