# Xtra Tests

This is a directory for tests not directly related to functionality within PHPCSUtils.

These tests safeguard functionality from PHP_CodeSniffer for which PHPCS itself does not have suffiencient test coverage.

In most cases, these type of tests should be pulled to PHP_CodeSniffer itself, but if testing this within the PHPCS native test framework would be difficult, tests can be placed here.
Tests can also be added here for functionality for which PHPCS just doesn't have enough tests or temporarily, while waiting for a test PR to be merged in PHPCS itself.

Note: these tests are run in CI in a separate test run called "risky" as these tests may start failing without notice due to changes in PHPCS itself.
