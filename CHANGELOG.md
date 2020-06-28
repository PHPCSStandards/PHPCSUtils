# Change Log for PHPCSUtils for PHP Codesniffer

All notable changes to this project will be documented in this file.

This projects adheres to [Keep a CHANGELOG](http://keepachangelog.com/) and uses [Semantic Versioning](http://semver.org/).


## [Unreleased]

_Nothing yet._


## [1.0.0-alpha3] - 2020-06-29

Notes:
* While still in alpha, some BC-breaks may be introduced. These are clearly indicated in the changelog with the :warning: symbol.
* Until PHPCS 4.x has been released, PHPCSUtils does not formally support it, though an effort is made to keep up with the changes and anticipate potential compatibility issues.
    For testing purposes, the composer configuration allows for PHPCSUtils to be installed with PHPCS 4.x.
* Until PHP 8.0 has been released, PHPCSUtils does not formally support it, though an effort is made to keep up with the changes and anticipate potential compatibility issues.
    For testing purposes, the composer configuration allows for PHPCSUtils to be installed with PHP 8.

### Added

* New [`PHPCSUtils\Utils\NamingConventions`][`NamingConventions`] class: Utility functions for working with identifier names (namespace names, class/trait/interface names, function names, variable and constant names). [#119](https://github.com/PHPCSStandards/PHPCSUtils/pull/119)
* New [`PHPCSUtils\BackCompat\Helper::getEncoding()`](https://phpcsutils.com/phpdoc/classes/PHPCSUtils-BackCompat-Helper.html#method_getEncoding) method. [#118](https://github.com/PHPCSStandards/PHPCSUtils/pull/118)
* New [`PHPCSUtils\Utils\ControlStructures::getCaughtExceptions()`](https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-ControlStructures.html#method_getCaughtExceptions) method. [#114](https://github.com/PHPCSStandards/PHPCSUtils/pull/114), [#138](https://github.com/PHPCSStandards/PHPCSUtils/pull/138)
* New [`PHPCSUtils\Utils\UseStatements::splitAndMergeImportUseStatement()`](https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-UseStatements.html#method_splitAndMergeImportUseStatement) method. [#117](https://github.com/PHPCSStandards/PHPCSUtils/pull/117)

#### PHPCS Backcompat
* `BCFile::getMethodProperties()`: support for "static" as a return type (PHP 8). [#134](https://github.com/PHPCSStandards/PHPCSUtils/pull/134) [PHPCS#2952](https://github.com/squizlabs/PHP_CodeSniffer/pull/2952)

#### TestUtils
* [`UtilityMethodTestCase`]: new public `$phpcsVersion` property for use in tests. [#107](https://github.com/PHPCSStandards/PHPCSUtils/pull/107)
    **Note**: if the PHPCS version is needed within a data provider method for a test, `Helper::getVersion()` still needs to be used as the data providers are run before the `setUpBeforeClass()`-like methods which set the property.

#### Tokens
* New [`Collections::$incrementDecrementOperators`][`Collections`] property. [#130](https://github.com/PHPCSStandards/PHPCSUtils/pull/130)
* New [`Collections::$magicConstants`][`Collections`] property. [#106](https://github.com/PHPCSStandards/PHPCSUtils/pull/106)
* New [`Collections::$objectOperators`][`Collections`] property. [#130](https://github.com/PHPCSStandards/PHPCSUtils/pull/130)
* New [`Collections::$OOHierarchyKeywords`][`Collections`] property representing the keywords to access properties or methods from inside a class definition, i.e `self`, `parent` and `static`. [#115](https://github.com/PHPCSStandards/PHPCSUtils/pull/115)
* New [`Collections::$OONameTokens`][`Collections`] property containing tokens which can be part of a partially/fully qualified name when used in inline code. [#113](https://github.com/PHPCSStandards/PHPCSUtils/pull/113)
* New [`Collections::functionDeclarationTokens()`][`Collections`] method to retrieve the tokens which represent a keyword starting a function declaration. [#133](https://github.com/PHPCSStandards/PHPCSUtils/pull/133)
    This method is compatible with PHPCS 3.5.3 and higher.
* New [`Collections::functionDeclarationTokensBC()`][`Collections`] method to retrieve the tokens which represent a keyword starting a function declaration (cross-version compatible). [#133](https://github.com/PHPCSStandards/PHPCSUtils/pull/133)
    This method is compatible with PHPCS 2.6.0 and higher.
* New [`Collections::parameterTypeTokensBC()`][`Collections`] method to retrieve the tokens which need to be recognized for parameter types cross-version. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)
    Use this method when the implementing standard needs to support PHPCS < 3.3.0.
* New [`Collections::propertyTypeTokensBC()`][`Collections`] method to retrieve the tokens which need to be recognized for property types cross-version. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)
    Use this method when the implementing standard needs to support PHPCS < 3.3.0.
* New [`Collections::returnTypeTokensBC()`][`Collections`] method to retrieve the tokens which need to be recognized for return types cross-version. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)
    Use this method when the implementing standard needs to support PHPCS < 3.5.4.
* `Collections::$returnTypeTokens`: support for "static" as a return type (PHP 8). [#134](https://github.com/PHPCSStandards/PHPCSUtils/pull/134)

#### Utils
* `FunctionDeclarations::getProperties()`: support for "static" as a return type (PHP 8). [#134](https://github.com/PHPCSStandards/PHPCSUtils/pull/134)

### Changed

#### PHPCS Backcompat
* `BCFile::getDeclarationName()`: has been made compatible with PHPCS 4.x. [#110](https://github.com/PHPCSStandards/PHPCSUtils/pull/110)
* `BCFile::getMethodProperties()`: has been made compatible with PHPCS 4.x. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)
* `BCFile::getMemberProperties()`: has been made compatible with PHPCS 4.x. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)
* `BCTokens`: :warning: The visibility of the `BCTokens::$phpcsCommentTokensTypes`, `BCTokens::$ooScopeTokens`, `BCTokens::$textStringTokens` properties has changed from `protected` to `private`. [#139](https://github.com/PHPCSStandards/PHPCSUtils/pull/139)
* `Helper::setConfigData()`: has been made compatible with PHPCS 4.x. [#137](https://github.com/PHPCSStandards/PHPCSUtils/pull/137)
    A new `$config` parameter has been added to the method. This parameter is a required parameter when the method is used with PHPCS 4.x.

#### TestUtils
* [`UtilityMethodTestCase`]: tests for JS/CSS will now automatically be skipped when run in combination with PHPCS 4.x (which drops JS/CSS support). [#111](https://github.com/PHPCSStandards/PHPCSUtils/pull/111)
* Confirmed that the currently available test utils are compatible with PHPUnit 9.x. [#103](https://github.com/PHPCSStandards/PHPCSUtils/pull/103)

#### Tokens
* `Collections::$parameterTypeTokens`: has been made compatible with PHPCS 4.x. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)
    :warning: This removes support for PHPCS < 3.3.0 from the property. Use the [`Collections::parameterTypeTokensBC()`][`Collections`] method instead if PHPCS < 3.3.0 needs to be supported.
* `Collections::$propertyTypeTokens`: has been made compatible with PHPCS 4.x. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)
    :warning: This removes support for PHPCS < 3.3.0 from the property. Use the [`Collections::propertyTypeTokensBC()`][`Collections`] method instead if PHPCS < 3.3.0 needs to be supported.
* `Collections::$returnTypeTokens`: has been made compatible with PHPCS 4.x. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)
    :warning: This removes support for PHPCS < 3.5.4 from the property. Use the [`Collections::returnTypeTokensBC()`][`Collections`] method instead if PHPCS < 3.5.4 needs to be supported.

#### Utils
* `FunctionDeclarations::getArrowFunctionOpenClose()`: has been made compatible with PHPCS 4.x. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)
* `FunctionDeclarations::getProperties()`: has been made compatible with PHPCS 4.x. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)
* :warning: `Lists::getAssignments()`: the return value of the method has been consolidated to be less fiddly to work with. [#129](https://github.com/PHPCSStandards/PHPCSUtils/pull/129)
    - :warning: The `nested_list` index key in the return value has been renamed to `is_nested_list`.
* `ObjectDeclarations::getName()`: has been made compatible with PHPCS 4.x. [#110](https://github.com/PHPCSStandards/PHPCSUtils/pull/110)
* `Variables::getMemberProperties()`: has been made compatible with PHPCS 4.x. [#109](https://github.com/PHPCSStandards/PHPCSUtils/pull/109)

#### Other
* Composer: PHPCSUtils can now be installed in combination with PHPCS `4.0.x-dev@dev` for testing purposes.
* Composer: The version requirements for the [DealerDirect Composer PHPCS plugin] have been widened to allow for version 0.7.0 which supports Composer 2.0.0.
* Readme/website homepage: textual improvements. Props [@GaryJones]. [#121](https://github.com/PHPCSStandards/PHPCSUtils/pull/121)
* Readme/website homepage: added additional FAQ question & answers. [#157](https://github.com/PHPCSStandards/PHPCSUtils/pull/157)
* The website homepage is now generated using the GitHub Pages gem with Jekyll, making maintenance easier. [#141](https://github.com/PHPCSStandards/PHPCSUtils/pull/141)
* Significant improvements to the docblock documentation and by extension the [generated API documentation](https://phpcsutils.com/phpdoc/). [#145](https://github.com/PHPCSStandards/PHPCSUtils/pull/145), [#146](https://github.com/PHPCSStandards/PHPCSUtils/pull/146), [#147](https://github.com/PHPCSStandards/PHPCSUtils/pull/147), [#148](https://github.com/PHPCSStandards/PHPCSUtils/pull/148), [#149](https://github.com/PHPCSStandards/PHPCSUtils/pull/149), [#150](https://github.com/PHPCSStandards/PHPCSUtils/pull/150), [#151](https://github.com/PHPCSStandards/PHPCSUtils/pull/151), [#152](https://github.com/PHPCSStandards/PHPCSUtils/pull/152), [#153](https://github.com/PHPCSStandards/PHPCSUtils/pull/153), [154](https://github.com/PHPCSStandards/PHPCSUtils/pull/154), [#155](https://github.com/PHPCSStandards/PHPCSUtils/pull/155), [#156](https://github.com/PHPCSStandards/PHPCSUtils/pull/156)
* Various housekeeping.

### Fixed

#### Abstract Sniffs
* `AbstractArrayDeclarationSniff`: improved parse error handling. [#99](https://github.com/PHPCSStandards/PHPCSUtils/pull/99)

#### PHPCS Backcompat
* `BCFile::findEndOfStatement()`: now supports arrow functions when used as a function argument, in line with the same change made in PHPCS 3.5.5. [#143](https://github.com/PHPCSStandards/PHPCSUtils/pull/143)
* `BcFile::isReference()`: bug fix, the reference operator was not recognized as such for closures declared to return by reference. [#160](https://github.com/PHPCSStandards/PHPCSUtils/pull/160) [PHPCS#2977](https://github.com/squizlabs/PHP_CodeSniffer/pull/2977)

#### Utils
* `FunctionDeclarations::getArrowFunctionOpenClose()`: now supports arrow functions when used as a function argument, in line with the same change made in PHPCS 3.5.5. [#143](https://github.com/PHPCSStandards/PHPCSUtils/pull/143)
* `FunctionDeclarations::getArrowFunctionOpenClose()`: now supports for arrow functions returning heredoc/nowdocs, in line with the same change made in PHPCS `master` and expected to be released in PHPCS 3.5.6. [#143](https://github.com/PHPCSStandards/PHPCSUtils/pull/143)
* `FunctionDeclarations::getName()`: bug fix for functions declared to return by reference. [#131](https://github.com/PHPCSStandards/PHPCSUtils/pull/131)
* `FunctionDeclarations::isMagicFunction()`: bug fix for nested functions. [#127](https://github.com/PHPCSStandards/PHPCSUtils/pull/127)
* `Operators::isReference()`: bug fix, the reference operator was not recognized as such for closures declared to return by reference. [#142](https://github.com/PHPCSStandards/PHPCSUtils/pull/142)
* `Namespaces::getType()`: improved type detection for when the `namespace` keyword is used as an operator in the global namespace. [#132](https://github.com/PHPCSStandards/PHPCSUtils/pull/132)
* `TextStrings::getCompleteTextString()`: will now remove the newline at the end of a heredoc/nowdoc. [#136](https://github.com/PHPCSStandards/PHPCSUtils/pull/136)
    PHP itself does not include the last new line in a heredoc/nowdoc text string when handling it, so the method shouldn't either.


## [1.0.0-alpha2] - 2020-02-16

Note:
* While still in alpha, some BC-breaks may be introduced. These are clearly indicated in the changelog with the :warning: symbol.

### Added

* New [`PHPCSUtils\Utils\ControlStructures`][`ControlStructures`] class: Utility functions for use when examining control structures. [#70](https://github.com/PHPCSStandards/PHPCSUtils/pull/70)
* New [`PHPCSUtils\Utils\FunctionDeclarations::isArrowFunction()`](https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-FunctionDeclarations.html#method_isArrowFunction) method. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)
* New [`PHPCSUtils\Utils\FunctionDeclarations::getArrowFunctionOpenClose()`](https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-FunctionDeclarations.html#method_getArrowFunctionOpenClose) method. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)

#### PHPCS Backcompat
* `BCFile::isReference()`: support for arrow functions returning by reference. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77)
* `BCFile::getMethodParameters()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)
* `BCFile::getMethodProperties()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79), [#89](https://github.com/PHPCSStandards/PHPCSUtils/pull/89)
* `BCFile::getDeclarationName()`: allow functions to be called "fn". [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77)
* `BCFile::findEndOfStatement()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77), [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)
* `BCFile::findStartOfStatement()`: support for arrow functions. [#77](https://github.com/PHPCSStandards/PHPCSUtils/pull/77)

#### Tokens
* New [`Collections::$alternativeControlStructureSyntaxTokens`][`Collections`] property. [#70](https://github.com/PHPCSStandards/PHPCSUtils/pull/70)
* New [`Collections::$alternativeControlStructureSyntaxCloserTokens`][`Collections`] property. [#68](https://github.com/PHPCSStandards/PHPCSUtils/pull/68), [#69](https://github.com/PHPCSStandards/PHPCSUtils/pull/69)
* New [`Collections::$controlStructureTokens`][`Collections`] property. [#70](https://github.com/PHPCSStandards/PHPCSUtils/pull/70)
* New [`Collections::arrowFunctionTokensBC()`][`Collections`] method. [#79](https://github.com/PHPCSStandards/PHPCSUtils/pull/79)

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
* [`AbstractArrayDeclarationSniff`]: to examine array declarations.

### Backcompat
* [`BCFile`]: Backport of the latest versions of PHPCS native utility functions from the `PHP_CodeSniffer\Files\File` class to make them available in older PHPCS versions without the bugs and other quirks that the older versions of the native functions had.
* [`BCTokens`]: Backport of the latest versions of PHPCS native token arrays from the `PHP_CodeSniffer\Util\Tokens` class to make them available in older PHPCS versions.
* [`Helper`]: Utility methods to retrieve (configuration) information from PHP_CodeSniffer 2.x as well as 3.x.

### Fixers
* [`SpacesFixer`]: Utility to check and, if necessary, fix the whitespace between two tokens.

### TestUtils
* [`UtilityMethodTestCase`]: Base class for use when testing utility methods for PHP_CodeSniffer.
    Compatible with both PHPCS 2.x as well as 3.x. Supports PHPUnit 4.x up to 8.x.
    See the usage instructions in the class docblock.

### Tokens
* [`Collections`]: Collections of related tokens as often used and needed for sniffs.
    These are additional "token groups" to compliment the ones available through the PHPCS native `PHP_CodeSniffer\Util\Tokens` class.

### Utils
* [`Arrays`]: Utility functions for use when examining arrays.
* [`Conditions`]: Utility functions for use when examining token conditions.
* [`FunctionDeclarations`]: Utility functions for use when examining function declaration statements.
* [`GetTokensAsString`]: Utility functions to retrieve the content of a set of tokens as a string.
* [`Lists`]: Utility functions to retrieve information when working with lists.
* [`Namespaces`]: Utility functions for use when examining T_NAMESPACE tokens and to determine the namespace of arbitrary tokens.
* [`Numbers`]: Utility functions for working with integer/float tokens.
* [`ObjectDeclarations`]: Utility functions for use when examining object declaration statements.
* [`Operators`]: Utility functions for use when working with operators.
* [`Orthography`]: Utility functions for checking the orthography of arbitrary text strings.
* [`Parentheses`]: Utility functions for use when examining parenthesis tokens and arbitrary tokens wrapped in parentheses.
* [`PassedParameters`]: Utility functions to retrieve information about parameters passed to function calls, array declarations, isset and unset constructs.
* [`Scopes`]: Utility functions for use when examining token scopes.
* [`TextStrings`]: Utility functions for working with text string tokens.
* [`UseStatements`]: Utility functions for examining use statements.
* [`Variables`]: Utility functions for use when examining variables.



[Unreleased]: https://github.com/PHPCSStandards/PHPCSUtils/compare/1.0.0-alpha3...HEAD
[1.0.0-alpha3]: https://github.com/PHPCSStandards/PHPCSUtils/compare/1.0.0-alpha2...1.0.0-alpha3
[1.0.0-alpha2]: https://github.com/PHPCSStandards/PHPCSUtils/compare/1.0.0-alpha1...1.0.0-alpha2

[DealerDirect Composer PHPCS plugin]: https://github.com/Dealerdirect/phpcodesniffer-composer-installer/

[`AbstractArrayDeclarationSniff`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-AbstractSniffs-AbstractArrayDeclarationSniff.html
[`BCFile`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-BackCompat-BCFile.html
[`BCTokens`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-BackCompat-BCTokens.html
[`Helper`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-BackCompat-Helper.html
[`SpacesFixer`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Fixers-SpacesFixer.html
[`UtilityMethodTestCase`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-TestUtils-UtilityMethodTestCase.html
[`Collections`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Tokens-Collections.html
[`Arrays`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-Arrays.html
[`Conditions`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-Conditions.html
[`ControlStructures`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-ControlStructures.html
[`FunctionDeclarations`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-FunctionDeclarations.html
[`GetTokensAsString`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-GetTokensAsString.html
[`Lists`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-Lists.html
[`Namespaces`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-Namespaces.html
[`NamingConventions`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-NamingConventions.html
[`Numbers`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-Numbers.html
[`ObjectDeclarations`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-ObjectDeclarations.html
[`Operators`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-Operators.html
[`Orthography`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-Orthography.html
[`Parentheses`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-Parentheses.html
[`PassedParameters`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-PassedParameters.html
[`Scopes`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-Scopes.html
[`TextStrings`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-TextStrings.html
[`UseStatements`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-UseStatements.html
[`Variables`]: https://phpcsutils.com/phpdoc/classes/PHPCSUtils-Utils-Variables.html

[@GaryJones]: https://github.com/GaryJones
