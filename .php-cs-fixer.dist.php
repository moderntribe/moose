<?php

$finder = (new PhpCsFixer\Finder())
    // ->in('wp-content/plugins/core')
    // ->in('wp-content/themes/core')
    ->in('wp-content/themes/core/blocks/tribe/terms/')
	->exclude(
		'dist',
		'patterns'
	);

$RuleSet = new vena\WordPress\PhpCsFixer\WordPressRuleSet();

return (new PhpCsFixer\Config())
	->registerCustomFixers( $RuleSet->getCustomFixers() )
	->registerCustomFixers( array(
		new vena\WordPress\PhpCsFixer\Fixer\WordPressCapitalPDangitFixer(),
	) )
	->setRiskyAllowed( $RuleSet->isRisky() )
	->setIndent( "\t" )
	->setRules(
		array_merge(
			$RuleSet->getRules(),
			array(
				// OPTIONAL. See below.
				'Vena/wp_capital_p_dangit' => true,
			)
		)
	)
    ->setFinder($finder);
