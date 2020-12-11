<?php

declare( strict_types = 1 );

namespace DNB\WikibaseConverter;

class PicaRecord {

	public function __construct(
		private array $jsonArray
	) {}

	/**
	 * @return array{array{name: string, subfields: array{array{name: string, value: string}}}}
	 */
	public function getFields(): array {
		return $this->jsonArray['fields'];
	}

}