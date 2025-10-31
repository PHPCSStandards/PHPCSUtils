<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\GHPages;

use RuntimeException;

/**
 * Prepare markdown documents for use in a GH Pages website before deploy.
 *
 * {@internal This functionality has a minimum PHP requirement of PHP 7.2.}
 *
 * @internal
 *
 * @phpcs:disable PHPCompatibility.Classes.NewConstVisibility.Found
 * @phpcs:disable PHPCompatibility.FunctionDeclarations.NewParamTypeDeclarations.intFound
 * @phpcs:disable PHPCompatibility.FunctionDeclarations.NewParamTypeDeclarations.stringFound
 * @phpcs:disable PHPCompatibility.FunctionDeclarations.NewReturnTypeDeclarations.intFound
 * @phpcs:disable PHPCompatibility.FunctionDeclarations.NewReturnTypeDeclarations.stringFound
 * @phpcs:disable PHPCompatibility.FunctionDeclarations.NewReturnTypeDeclarations.voidFound
 * @phpcs:disable PHPCompatibility.InitialValue.NewConstantScalarExpressions.constFound
 */
final class UpdateWebsite
{

    /**
     * Path to project root (without trailing slash).
     *
     * @var string
     */
    private const PROJECT_ROOT = __DIR__ . '/../..';

    /**
     * Relative path to target directory off project root (without trailing slash).
     *
     * @var string
     */
    private const TARGET_DIR = 'docs';

    /**
     * Frontmatter for the website homepage.
     *
     * @var string
     */
    private const README_FRONTMATTER = '---
title:       PHPCSUtils
description: "PHPCSUtils: A suite of utility functions for use with PHP_CodeSniffer"
anchor:      home
permalink:   /
seo:
    type: WebSite
    publisher:
        type: Organisation
---
';

    /**
     * Frontmatter for the changelog page.
     *
     * @var string
     */
    private const CHANGELOG_FRONTMATTER = '---
title:       Changelog
description: "Changelog for the PHPCSUtils suite of utility functions for use with PHP_CodeSniffer"
anchor:      changelog
permalink:   /changelog
seo:
    type: WebSite
    publisher:
        type: Organisation
---
';

    /**
     * Resolved path to project root (with trailing slash).
     *
     * @var string
     */
    private $realRoot;

    /**
     * Resolved path to target directory (with trailing slash).
     *
     * @var string
     */
    private $realTarget;

    /**
     * Run the transformation.
     *
     * @return int Exit code.
     */
    public function run(): int
    {
        $exitcode = 0;

        try {
            $this->setPaths();
            $this->transformReadme();
            $this->transformChangelog();
        } catch (RuntimeException $e) {
            echo 'ERROR: ', $e->getMessage(), \PHP_EOL;
            $exitcode = 1;
        }

        return $exitcode;
    }

    /**
     * Validate the paths to use.
     *
     * @return void
     */
    private function setPaths(): void
    {
        $realRoot = \realpath(self::PROJECT_ROOT);
        if ($realRoot === false) {
            throw new RuntimeException(\sprintf('Failed to find the %s directory.', self::PROJECT_ROOT));
        }

        $this->realRoot = $realRoot . '/';

        // Check if the target directory exists and if not, create it.
        $targetDir = $this->realRoot . self::TARGET_DIR;

        if (@\is_dir($targetDir) === false) {
            if (@\mkdir($targetDir, 0777, true) === false) {
                throw new RuntimeException(\sprintf('Failed to create the %s directory.', $targetDir));
            }
        }

        $realPath = \realpath($targetDir);
        if ($realPath === false) {
            throw new RuntimeException(\sprintf('Failed to find the %s directory.', $targetDir));
        }

        $this->realTarget = $realPath . '/';
    }

    /**
     * Apply various transformations to the index page.
     *
     * - Remove title, badges and index.
     * - Replace code samples with properly highlighted versions.
     * - Add frontmatter.
     *
     * @return void
     *
     * @throws \RuntimeException When any of the expected replacements could not be made.
     */
    private function transformReadme(): void
    {
        $contents = $this->getContents($this->realRoot . 'README.md');

        // Remove title, badges and index.
        $contents = $this->replace('`^.*## Features`s', '## Features', $contents, 1);

        // Remove the section about Non-Composer based integration.
        $contents = $this->replace(
            '`### Non-Composer based integration[\n\r]+(?:.+[\n\r]+)+?## Frequently Asked Questions`',
            '## Frequently Asked Questions',
            $contents,
            1
        );

        // Replace installation instructions with properly highlighted version.
        $search   = '~`{3}bash[\n\r]+composer config allow-plugins.dealerdirect/phpcodesniffer-composer-installer'
            . ' true[\n\r]+'
            . 'composer require phpcsstandards/phpcsutils:"([^\n\r]+)"[\n\r]+`{3}~';
        $replace  = '<div class="language-bash highlighter-rouge"><div class="highlight"><pre class="highlight"><code>'
            . 'composer config <span class="s">allow-plugins.dealerdirect/phpcodesniffer-composer-installer</span>'
            . ' <span class="mf">true</span>'
            . "\n"
            . 'composer require <span class="s">{{ site.phpcsutils.packagist }}</span>:"<span class="mf">$1</span>"'
            . "\n"
            . '</code></pre></div></div>';
        $contents = $this->replace($search, $replace, $contents, 1);

        // Replace suggested end-user installation instructions with properly highlighted versions.
        $search   = '~`{3}bash[\r\n]+> composer config allow-plugins.dealerdirect/phpcodesniffer-composer-installer'
            . ' true[\r\n]+> `{3}~';
        $replace  = '<div class="language-bash highlighter-rouge"><div class="highlight"><pre class="highlight"><code>'
            . 'composer config <span class="s">allow-plugins.dealerdirect/phpcodesniffer-composer-installer</span>'
            . ' <span class="mf">true</span>'
            . "\n"
            . '> </code></pre></div></div>';
        $contents = $this->replace($search, $replace, $contents, 1);

        // Replace suggested end-user upgrade instructions with properly highlighted versions.
        $search   = '~`{3}bash[\r\n]+> composer update your/cs-package --with-\[all-\]dependencies[\r\n]+> `{3}~';
        $replace  = '<div class="language-bash highlighter-rouge"><div class="highlight"><pre class="highlight"><code>'
            . 'composer update <span class="s">your/cs-package</span> <span class="mf">--with-[all-]dependencies</span>'
            . "\n"
            . '> </code></pre></div></div>';
        $contents = $this->replace($search, $replace, $contents, 1);

        // Add frontmatter.
        $contents = self::README_FRONTMATTER . "\n" . $contents;

        $this->putContents($this->realTarget . 'index.md', $contents);
    }

    /**
     * Add frontmatter to the changelog page and remove "Unreleased".
     *
     * @return void
     */
    private function transformChangelog(): void
    {
        $contents = $this->getContents($this->realRoot . 'CHANGELOG.md');

        // Remove the section about Non-Composer based integration.
        $contents = $this->replace(
            '`## \[Unreleased\][\n\r]+(?:.+[\n\r]+)+?##`',
            '##',
            $contents,
            1
        );

        // Add frontmatter.
        $contents = self::CHANGELOG_FRONTMATTER . "\n" . $contents;

        $this->putContents($this->realTarget . 'changelog.md', $contents);
    }

    /**
     * Execute a regex search and replace and verify the replacement was actually made.
     *
     * @param string $search  The pattern to search for.
     * @param string $replace The replacement.
     * @param string $subject The string to execute the search & replace on.
     * @param int    $limit   Maximum number of replacements to make.
     *
     * @return string
     *
     * @throws \RuntimeException When the replacement was not made or not made the required number of times.
     */
    private function replace(string $search, string $replace, string $subject, int $limit = 1): string
    {
        $subject = \preg_replace($search, $replace, $subject, $limit, $count);
        if ($count !== $limit) {
            throw new RuntimeException(
                'Failed to make required replacement.' . \PHP_EOL
                . "Search regex: $search" . \PHP_EOL
                . "Replacements made: $count"
            );
        }

        return $subject;
    }

    /**
     * Retrieve the contents of a file.
     *
     * @param string $source Path to the source file.
     *
     * @return string
     *
     * @throws \RuntimeException When the contents of the file could not be retrieved.
     */
    private function getContents(string $source): string
    {
        $contents = \file_get_contents($source);
        if (\is_string($contents) === false) {
            throw new RuntimeException(\sprintf('Failed to read doc file: %s', $source));
        }

        return $contents;
    }

    /**
     * Write a string to a file.
     *
     * @param string $target   Path to the target file.
     * @param string $contents File contents to write.
     *
     * @return void
     *
     * @throws \RuntimeException When the target directory could not be created.
     * @throws \RuntimeException When the file could not be written to the target directory.
     */
    private function putContents(string $target, string $contents): void
    {
        // Check if the target directory exists and if not, create it.
        $targetDir = \dirname($target);

        if (@\is_dir($targetDir) === false) {
            if (@\mkdir($targetDir, 0777, true) === false) {
                throw new RuntimeException(\sprintf('Failed to create the %s directory.', $targetDir));
            }
        }

        // Make sure the file always ends on a new line.
        $contents = \rtrim($contents) . "\n";
        if (\file_put_contents($target, $contents) === false) {
            throw new RuntimeException(\sprintf('Failed to write to target location: %s', $target));
        }
    }
}
