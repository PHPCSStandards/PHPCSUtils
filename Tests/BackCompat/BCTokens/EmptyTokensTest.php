<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCTokens;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\BackCompat\Helper;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class EmptyTokensTest extends TestCase
{

    /**
     * Tokens that are comments.
     *
     * @var array <int|string> => <int|string>
     */
    protected $commentTokens = [
        \T_COMMENT                => \T_COMMENT,
        \T_DOC_COMMENT            => \T_DOC_COMMENT,
        \T_DOC_COMMENT_STAR       => \T_DOC_COMMENT_STAR,
        \T_DOC_COMMENT_WHITESPACE => \T_DOC_COMMENT_WHITESPACE,
        \T_DOC_COMMENT_TAG        => \T_DOC_COMMENT_TAG,
        \T_DOC_COMMENT_OPEN_TAG   => \T_DOC_COMMENT_OPEN_TAG,
        \T_DOC_COMMENT_CLOSE_TAG  => \T_DOC_COMMENT_CLOSE_TAG,
        \T_DOC_COMMENT_STRING     => \T_DOC_COMMENT_STRING,
    ];

    /**
     * Token types that are comments containing PHPCS instructions.
     *
     * @var array <string> => <string>
     */
    protected $phpcsCommentTokens = [
        'PHPCS_T_PHPCS_ENABLE'      => 'PHPCS_T_PHPCS_ENABLE',
        'PHPCS_T_PHPCS_DISABLE'     => 'PHPCS_T_PHPCS_DISABLE',
        'PHPCS_T_PHPCS_SET'         => 'PHPCS_T_PHPCS_SET',
        'PHPCS_T_PHPCS_IGNORE'      => 'PHPCS_T_PHPCS_IGNORE',
        'PHPCS_T_PHPCS_IGNORE_FILE' => 'PHPCS_T_PHPCS_IGNORE_FILE',
    ];

    /**
     * Test the Tokens::emptyTokens() method.
     *
     * @covers \PHPCSUtils\BackCompat\BCTokens::__callStatic
     *
     * @return void
     */
    public function testEmptyTokens()
    {
        $version  = Helper::getVersion();
        $expected = [\T_WHITESPACE => \T_WHITESPACE] + $this->commentTokens;

        if (\version_compare($version, '3.2.0', '>=') === true) {
            $expected += $this->phpcsCommentTokens;
        }

        $this->assertSame($expected, BCTokens::emptyTokens());
    }

    /**
     * Test the Tokens::commentTokens() method.
     *
     * @covers \PHPCSUtils\BackCompat\BCTokens::__callStatic
     *
     * @return void
     */
    public function testCommentTokens()
    {
        $version  = Helper::getVersion();
        $expected = $this->commentTokens;

        if (\version_compare($version, '3.2.0', '>=') === true) {
            $expected += $this->phpcsCommentTokens;
        }

        $this->assertSame($expected, BCTokens::commentTokens());
    }

    /**
     * Test the Tokens::phpcsCommentTokens() method.
     *
     * @covers \PHPCSUtils\BackCompat\BCTokens::phpcsCommentTokens
     *
     * @return void
     */
    public function testPhpcsCommentTokens()
    {
        $version  = Helper::getVersion();
        $expected = [];

        if (\version_compare($version, '3.2.0', '>=') === true) {
            $expected = $this->phpcsCommentTokens;
        }

        $this->assertSame($expected, BCTokens::phpcsCommentTokens());
    }

    /**
     * Test whether the method in BCTokens is still in sync with the latest version of PHPCS.
     *
     * This group is not run by default and has to be specifically requested to be run.
     *
     * @group compareWithPHPCS
     *
     * @covers \PHPCSUtils\BackCompat\BCTokens::__callStatic
     *
     * @return void
     */
    public function testPHPCSEmptyTokens()
    {
        $this->assertSame(Tokens::$emptyTokens, BCTokens::emptyTokens());
    }

    /**
     * Test whether the method in BCTokens is still in sync with the latest version of PHPCS.
     *
     * This group is not run by default and has to be specifically requested to be run.
     *
     * @group compareWithPHPCS
     *
     * @covers \PHPCSUtils\BackCompat\BCTokens::__callStatic
     *
     * @return void
     */
    public function testPHPCSUpstreamCommentTokens()
    {
        $this->assertSame(Tokens::$commentTokens, BCTokens::commentTokens());
    }

    /**
     * Test whether the method in BCTokens is still in sync with the latest version of PHPCS.
     *
     * This group is not run by default and has to be specifically requested to be run.
     *
     * @group compareWithPHPCS
     *
     * @covers \PHPCSUtils\BackCompat\BCTokens::phpcsCommentTokens
     *
     * @return void
     */
    public function testPHPCSPhpcsCommentTokens()
    {
        $this->assertSame(Tokens::$phpcsCommentTokens, BCTokens::phpcsCommentTokens());
    }
}
