<?php declare(strict_types = 1);

namespace Thunbolt\Doctrine\DI;

use Nette\DI\CompilerExtension;
use Thunbolt\Doctrine\Functions\DateFunction;
use Thunbolt\Doctrine\Functions\GroupConcat;
use Thunbolt\Doctrine\Functions\MatchAgainst;
use Thunbolt\Doctrine\Functions\Rand;

final class DoctrineExtension extends CompilerExtension {

	/** @var mixed[] */
	public $defaults = [
		'functions' => [
			'date' => [
				'enabled' => false,
				'name' => 'DATE',
				'type' => 'string',
				'class' => DateFunction::class,
			],
			'match' => [
				'enabled' => false,
				'name' => 'MATCH',
				'type' => 'string',
				'class' => MatchAgainst::class,
			],
			'rand' => [
				'enabled' => false,
				'name' => 'RAND',
				'type' => 'numeric',
				'class' => Rand::class
			],
			'group_concat' => [
				'enabled' => false,
				'name' => 'GROUP_CONCAT',
				'type' => 'string',
				'class' => GroupConcat::class,
			]
		], // date, matchAgainst, rand
		'configurationDefinition' => 'orm.configuration'
	];

	public function beforeCompile(): void {
		$builder = $this->getContainerBuilder();
		$config = $this->validateConfig($this->defaults);

		$configurator = $builder->getDefinition($config['configurationDefinition']);

		foreach ($config['functions'] as $options) {
			if (!$options['enabled']) {
				continue;
			}

			$method = sprintf('addCustom%sFunction', ucfirst($options['type']));

			$configurator->addSetup($method, [$options['name'], $options['class']]);
		}
	}

}
