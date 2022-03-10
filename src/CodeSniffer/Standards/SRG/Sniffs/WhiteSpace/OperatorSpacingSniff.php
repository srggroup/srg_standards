<?php

namespace SRG\Sniffs\WhiteSpace;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Standards\Squiz\Sniffs\WhiteSpace\OperatorSpacingSniff as SquizOperatorSpacingSniff;

/**
 * Class OperatorSpacing
 *
 * @author    SRG Group <dev@srg.hu>
 * @copyright 2022 SRG Group Kft.
 */
class OperatorSpacingSniff extends SquizOperatorSpacingSniff {


	/**
	 * Exclude catch exception pipes
	 *
	 * @param File $phpcsFile
	 * @param      $stackPtr
	 *
	 * @return bool
	 */
	protected function isOperator(File $phpcsFile, $stackPtr) {
		$return = parent::isOperator($phpcsFile, $stackPtr);

		$tokens = $phpcsFile->getTokens();

		if ($tokens[$stackPtr]['code'] === T_USE) {
			echo '';
		}

		if ($tokens[$stackPtr]['code'] === T_BITWISE_OR &&
			count($tokens[$stackPtr]['nested_parenthesis']) === 1 &&
			$tokens[array_key_first($tokens[$stackPtr]['nested_parenthesis']) - 2]['code'] === T_CATCH
		) {
			return false;
		}

		return $return;
	}


}
