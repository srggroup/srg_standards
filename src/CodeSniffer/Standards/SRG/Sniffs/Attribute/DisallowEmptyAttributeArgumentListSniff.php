<?php

declare(strict_types=1);

namespace SRG\Sniffs\Attribute;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowEmptyAttributeArgumentListSniff implements Sniff {


	/**
	 * Only run when we encounter an attribute token.
	 *
	 * @return array<int>
	 */
	public function register(): array {
		return [T_ATTRIBUTE];
	}


	/**
	 * Examines the attribute, and if there are empty parentheses, reports an error and provides a fix.
	 *
	 * @param int $stackPtr The token index in the token stack
	 */
	public function process(File $phpcsFile, $stackPtr): void {
		$tokens = $phpcsFile->getTokens();

		// Find the first '(' token after the attribute start
		$openParen = $phpcsFile->findNext(
			T_OPEN_PARENTHESIS,
			$stackPtr + 1,
			null,
			false,
			null,
			true
		);

		if ($openParen === false) {
			return;
		}

		// Make sure this is the attribute's opening parenthesis,
		// i.e., that it has a corresponding closing parenthesis.
		$closeParen = $tokens[$openParen]['parenthesis_closer'] ?? null;
		if ($closeParen === null) {
			return;
		}

		// Check if there's really a *direct* closing parenthesis: nothing between them (not even whitespace)
		// Important: whitespace can also be tokens — might be worth checking trim rules too.
		$nextAfterOpen = $openParen + 1;
		if (isset($tokens[$nextAfterOpen]) && $tokens[$nextAfterOpen]['code'] === T_CLOSE_PARENTHESIS) {
			// Found empty ()
			$error = 'Empty parentheses in attribute are not allowed; use #[Foo] instead of #[Foo()]';
			$fix = $phpcsFile->addFixableError($error, $openParen, 'EmptyArgsInAttribute');

			if ($fix) {
				$phpcsFile->fixer->beginChangeset();
				// Delete the '(' and ')' tokens — if there's whitespace between them, you can handle that more elegantly
				$phpcsFile->fixer->replaceToken($openParen, '');
				$phpcsFile->fixer->replaceToken($closeParen, '');
				$phpcsFile->fixer->endChangeset();
			}
		}
	}


}
