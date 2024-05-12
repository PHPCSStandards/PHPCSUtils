<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Values;

use PHP_CodeSniffer\Files\File;
use PHPCSUtils\Exceptions\LogicException;
use PHPCSUtils\Exceptions\OutOfBoundsStackPtr;
use PHPCSUtils\Exceptions\RuntimeException;
use PHPCSUtils\Exceptions\TypeError;
use PHPCSUtils\Exceptions\ValueError;
use PHPCSUtils\Values\ValueObject;

/**
 * Represents a namespace found within a file.
 *
 * ---------------------------------------------------------------------------------------------
 * This class is only intended for internal use by PHPCSUtils and is not part of the public API.
 * This also means that it has no promise of backward compatibility.
 * ---------------------------------------------------------------------------------------------
 *
 * @internal
 *
 * @since 1.1.0
 */
final class NamespaceName implements ValueObject
{

    /**
     * File object this namespace was found in.
     *
     * @since 1.1.0
     *
     * @var \PHP_CodeSniffer\Files\File
     */
    private $currentFile;

    /**
     * The namespace name.
     *
     * Note: this can be an empty string for the global namespace.
     *
     * @since 1.1.0
     *
     * @var string
     */
    private $name;

    /**
     * Stack pointer to the effective start of the namespace.
     *
     * @since 1.1.0
     *
     * @var int
     */
    private $start;

    /**
     * Stack pointer to the effective end of the namespace; or NULL if the end of the namespace
	 * is not (yet) known (unscoped namespace).
     *
     * @since 1.1.0
     *
     * @var int|null
     */
    private $end;

	/**
	 * Constructor.
     *
     * @since 1.1.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the namespace was found.
	 * @param string                      $name      The namespace name.
	 * @param int                         $start     Stack pointer to the effective start of the namespace.
	 * @param int|null                    $end       Stack pointer to the effective end of the namespace;
	 *                                               or NULL if the end of the namespace is not (yet) known.
	 */
	public function __construct(File $phpcsFile, $name, $start, $end = null)
	{
		if (\is_string($name) === false) {
			throw TypeError::create(4, '$name', 'string', $name);
		}

		if (\is_int($start) === false) {
			throw TypeError::create(2, '$start', 'integer', $start);
		}

		if (\is_int($end) === false && $end !== null) {
			throw TypeError::create(3, '$end', 'integer|null', $end);
		}

		$tokens = $phpcsFile->getTokens();

		if (isset($tokens[$start]) === false) {
			throw OutOfBoundsStackPtr::create(2, '$start', $start);
		}

		if (\is_int($end) && isset($tokens[$end]) === false) {
			throw OutOfBoundsStackPtr::create(3, '$end', $end);
		}

		if (\is_int($end) && $end <= $start) {
			throw ValueError::create(3, '$end', \sprintf('must be higher the $start value; $start is %d, $end is %d', $start, $end));
		}

		$this->currentFile = $phpcsFile;
		$this->start       = $start;
		$this->end         = $end;
		$this->name        = $name;
	}
	
	/**
	 * Check whether a property value is set.
     *
     * @since 1.1.0
	 *
	 * @param string $name Name of the property.
	 *
	 * @return bool
	 */
	public function __isset($name)
	{
		return isset($this->$name);
	}

	/**
	 * Retrieve the value of a property.
     *
     * @since 1.1.0
	 *
	 * @param string $name Name of the property.
	 *
	 * @return mixed
	 */
	public function __get($name)
	{
		if (\property_exists($this, $name)) {
			return $this->$name;
		}

		throw new RuntimeException(\sprintf('Requested property %s does not exist', $name));
	}

	/**
     * Get a string representation of the object.
     *
     * @since 1.1.0
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->name;
	}

	/**
     * Check whether two File objects represent the same file.
     *
     * @since 1.1.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile A PHP_CodeSniffer file.
	 *
	 * @return bool
	 */
	private function sameFile(File $phpcsFile)
	{
		return $this->currentFile->getFilename() === $phpcsFile->getFilename();
	}

	/**
	 * Check whether an arbitrary stack pointer is within this namespace.
     *
     * @since 1.1.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the namespace was found.
	 * @param int                         $stackPtr  Arbitrary stack pointer within the current file.
	 *
	 *
	 * @return bool
	 */
	public function isWithin(File $phpcsFile, $stackPtr)
	{
		if (\is_int($stackPtr) === false) {
			throw TypeError::create(2, '$stackPtr', 'int', $stackPtr);
		}

		if ($this->sameFile($phpcsFile) === false) {
			return false;
		}

		return $this->start <= $stackPtr && ($this->end === null || $stackPtr >= $this->end);
	}

	/**
     * Close an open namespace.
     *
     * @since 1.1.0
	 *
	 * @param \PHP_CodeSniffer\Files\File $phpcsFile The PHP_CodeSniffer file where the namespace was found.
	 * @param int                         $end       Stack pointer to the effective end of the namespace.
	 *
	 * @return \PHPCSUtils\Values\NamespaceName
	 */
	public function close(File $phpcsFile, $end)
	{
		if ($this->end !== null) {
			throw LogicException::create('Can\'t close an already closed namespace');
		}

		if ($this->sameFile($phpcsFile) === false) {
			throw LogicException::create('Can\'t close a namespace for a different file');
		}

		return new self($this->phpcsFile, $this->start, $end, $this->name);
	}

	/**
	 * Compare two NamespaceName objects.
	 *
	 * Mostly useful for testing.
     *
     * @since 1.1.0
	 *
	 * @param \PHPCSUtils\Values\NamespaceName $namespaceName Another NamespaceName object.
	 *
	 * @return bool
	 */
	public function equals(self $namespaceName)
	{
		return $this->sameFile($namespaceName->currentFile)
		    && $this->start === $namespaceName->start
		    && $this->end === $namespaceName->end
		    && $this->name === $namespaceName->name;
	}
}
