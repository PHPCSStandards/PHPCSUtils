<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Utils;

/**
 * Utility functions for working with identifier names.
 *
 * Identifiers in PHP are namespace names, class/trait/interface names, function names,
 * variable names and constant names.
 *
 * @since 1.0.0
 */
class NamingConventions
{

    /**
     * Regular expression to check if a given identifier name is valid for use in PHP.
     *
     * @link http://php.net/manual/en/language.variables.basics.php
     * @link http://php.net/manual/en/language.constants.php
     * @link http://php.net/manual/en/functions.user-defined.php
     * @link http://php.net/manual/en/language.oop5.basic.php
     *
     * @since 1.0.0
     *
     * @var string
     */
    const PHP_LABEL_REGEX = '`^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$`';

    /**
     * Uppercase A-Z.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const AZ_UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

    /**
     * Lowercase a-z.
     *
     * @since 1.0.0
     *
     * @var string
     */
    const AZ_LOWER = 'abcdefghijklmnopqrstuvwxyz';

    /**
     * Verify whether an arbitrary text string is valid as an identifier name in PHP.
     *
     * For variable names: the leading `$` needs to be removed prior to passing the name to this method.
     *
     * @since 1.0.0
     *
     * @param string $name The name.
     *
     * @return bool
     */
    public static function isValidIdentifierName($name)
    {
        if (\is_string($name) === false || $name === '' || \strpos($name, ' ') !== false) {
            return false;
        }

        return (\preg_match(self::PHP_LABEL_REGEX, $name) === 1);
    }

    /**
     * Check if two arbitrary identifier names will be seen as the same in PHP.
     *
     * This method should not be used for variable or constant names, but *should* be used
     * when comparing namespace, class/trait/interface and function names.
     *
     * Variable and constant names in PHP are case-sensitive, except for constants explicitely
     * declared case-insensitive using the third parameter for `define()`.
     *
     * All other names are case-insensitive for the most part, but as it's PHP, not completely.
     * Basically ASCII chars used are case-insensitive, but anything from 0x80 up is case-sensitive.
     *
     * This method takes this case-(in)sensitivity into account when comparing identifier names.
     *
     * Note: this method does not check whether the passed names would be valid for identifiers!
     * See the {@see \PHPCSUtils\Utils\NamingConventions::isValidIdentifierName()} method.
     *
     * @since 1.0.0
     *
     * @param string $nameA The first identifier name.
     * @param string $nameB The second identifier name.
     *
     * @return bool TRUE if these names would be considered the same in PHP, FALSE otherwise.
     */
    public static function isEqual($nameA, $nameB)
    {
        // Simple quick check first.
        if ($nameA === $nameB) {
            return true;
        }

        // OK, so these may be different names or they may be the same name with case differences.
        $nameA = \strtr($nameA, self::AZ_UPPER, self::AZ_LOWER);
        $nameB = \strtr($nameB, self::AZ_UPPER, self::AZ_LOWER);

        return ($nameA === $nameB);
    }
}
