# Change Log for PHPCSUtils for PHP Codesniffer

All notable changes to this project will be documented in this file.

This projects adheres to [Keep a CHANGELOG](http://keepachangelog.com/) and uses [Semantic Versioning](http://semver.org/).


## [Unreleased]

_Nothing yet._


## [1.0.0-alpha2] - 2020-02-16

### Added

* New `PHPCSUtils\Utils\ControlStructures` class: Utility functions for use when examining control structures. [#70](https://github.com/PHPCSStandards/PHPCSUtils/pull/70)
* New `PHPCSUtils\Utils\FunctionDeclarations::isArrowFunction()` method. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)
* New `PHPCSUtils\Utils\FunctionDeclarations::getArrowFunctionOpenClose()` method. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)

#### PHPCS Backcompat
* `BCFile::isReference()`: support for arrow functions returning by reference. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77)
* `BCFile::getMethodParameters()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)
* `BCFile::getMethodProperties()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79), [#89](https://github.com/PHPCSStandards/PHPCSUtils/pull/89)
* `BCFile::getDeclarationName()`: allow functions to be called "fn". [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77)
* `BCFile::findEndOfStatement()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)
* `BCFile::findStartOfStatement()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77)

#### Tokens
* New `Collections::$alternativeControlStructureSyntaxTokens` property. [#70](https://github.com/PHPCSStandards/PHPCSUtils/pull/70)
* New `Collections::$alternativeControlStructureSyntaxCloserTokens` property. [#68](https://github.com/PHPCSStandards/PHPCSUtils/pull/68), [#69](https://github.com/PHPCSStandards/PHPCSUtils/pull/69)
* New `Collections::$controlStructureTokens` property. [#70](https://github.com/PHPCSStandards/PHPCSUtils/pull/70)
* New `Collections::arrowFunctionTokensBC()` method. [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)

#### Utils
* `Arrays::getDoubleArrowPtr()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79), [#84](https://github.com/PHPCSStandards/PHPCSUtils/pull/84)
* `FunctionDeclarations::getParameters()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)
* `FunctionDeclarations::getProperties()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)
* `Operators::isReference()`: support for arrow functions returning by reference. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77)
* `Parentheses::getOwner()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77)
* `Parentheses::isOwnerIn()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)

#### Other
* Documentation website at https://phpcsutils.com/

### Changed

#### PHPCS Backcompat
* `BCFile::getCondition()`: sync with PHPCS 3.5.4 - added support for new `$first` parameter. [#73](https://github.com/PHPCSStandards/PHPCSUtils/pull/73)

#### Tokens
* The `Collections::$returnTypeTokens` property now includes `T_ARRAY` to allow for supporting arrow functions in PHPCS < 3.5.3. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77)

#### Utils
* :warning: `Conditions::getCondition()`: sync with PHPCS 3.5.4 - renamed the existing `$reverse` parameter to `$first` and reversing the meaning of the boolean values, to stay in line with PHPCS itself. [#73](https://github.com/PHPCSStandards/PHPCSUtils/pull/73)
* :warning: `Numbers`: the `$unsupportedPHPCSVersions` property has been replaced with an `UNSUPPORTED_PHPCS_VERSION` constant.

#### Other
* Various housekeeping.


## 1.0.0-alpha1 - 2020-01-23

Initial alpha release containing:
* A `PHPCS23Utils` standard which can be used to allow an external PHPCS standard to be compatible with both PHPCS 2.x as well as 3.x.
* A `PHPCSUtils` standard which contains generic utilities which can be used when writing sniffs.
    **_This standard does not contain any sniffs!_**
    To use these utilities in PHPCS 3.x, all that is needed is for this package to be installed and registered with PHPCS using `installed_paths`. If the package is requested via Composer, this will automatically be handled by the [DealerDirect Composer PHPCS plugin].
    To use these utilities in PHPCS 2.x, make sure the external standard includes the `PHPCS23Utils` standard in the `ruleset.xml` file like so: `<rule ref="PHPCS23Utils"/>`.

All utilities offered are compatible with PHP_CodeSniffer 2.6.0 up to the latest stable release.

This initial alpha release contains the following utility classes:

### Abstract Sniffs
* `AbstractArrayDeclarationSniff`: to examine array declarations.

### Backcompat
* `BCFile`: Backport of the latest versions of PHPCS native utility functions from the `PHP_CodeSniffer\Files\File` class to make them available in older PHPCS versions without the bugs and other quirks that the older versions of the native functions had.
* `BCTokens`: Backport of the latest versions of PHPCS native token arrays from the `PHP_CodeSniffer\Util\Tokens` class to make them available in older PHPCS versions.
* `Helper`: Utility methods to retrieve (configuration) information from PHP_CodeSniffer 2.x as well as 3.x.

### Fixers
* `SpacesFixer`: Utility to check and, if necessary, fix the whitespace between two tokens.

### TestUtils
* `UtilityMethodTestCase`: Base class for use when testing utility methods for PHP_CodeSniffer.
    Compatible with both PHPCS 2.x as well as 3.x. Supports PHPUnit 4.x up to 8.x.
    See the usage instructions in the class docblock.

### Tokens
* `Collections`: Collections of related tokens as often used and needed for sniffs.
    These are additional "token groups" to compliment the ones available through the PHPCS native `PHP_CodeSniffer\Util\Tokens` class.

### Utils
* `Arrays`: Utility functions for use when examining arrays.
* `Conditions`: Utility functions for use when examining token conditions.
* `FunctionDeclarations`: Utility functions for use when examining function declaration statements.
* `GetTokensAsString`: Utility functions to retrieve the content of a set of tokens as a string.
* `Lists`: Utility functions to retrieve information when working with lists.
* `Namespaces`: Utility functions for use when examining T_NAMESPACE tokens and to determine the namespace of arbitrary tokens.
* `Numbers`: Utility functions for working with integer/float tokens.
* `ObjectDeclarations`: Utility functions for use when examining object declaration statements.
* `Operators`: Utility functions for use when working with operators.
* `Orthography`: Utility functions for checking the orthography of arbitrary text strings.
* `Parentheses`: Utility functions for use when examining parenthesis tokens and arbitrary tokens wrapped in parentheses.
* `PassedParameters`: Utility functions to retrieve information about parameters passed to function calls, array declarations, isset and unset constructs.
* `Scopes`: Utility functions for use when examining token scopes.
* `TextStrings`: Utility functions for working with text string tokens.
* `UseStatements`: Utility functions for examining use statements.
* `Variables`: Utility functions for use when examining variables.


[DealerDirect Composer PHPCS plugin]: https://github.com/Dealerdirect/phpcodesniffer-composer-installer/


[Unreleased]: https://github.com/PHPCSStandards/PHPCSUtils/compare/1.0.0-alpha2...HEAD
[1.0.0-alpha2]: https://github.com/PHPCSStandards/PHPCSUtils/compare/1.0.0-alpha1...1.0.0-alpha2

