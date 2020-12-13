<?php

declare( strict_types = 1 );

namespace DNB\WikibaseConverter;

class MappingDeserializer {

	public function jsonArrayToObject( array $json ): Mapping {
		return new Mapping(
			$this->fieldMappingsFromJsonArray( $json ),
			$this->propertyDefinitionsFromJsonArray( $json )
		);
	}

	private function fieldMappingsFromJsonArray( array $json ): PicaFieldMappingList {
		$fieldMappings = [];

		foreach ( $json as $picaField => $mappings ) {
			$fieldMappings[] = new PicaFieldMapping(
				name: $picaField,
				propertyMappings: $this->propertyMappingsFromJsonArray( $mappings )
			);
		}

		return new PicaFieldMappingList( ...$fieldMappings );
	}

	/**
	 * @return PropertyMapping[]
	 */
	private function propertyMappingsFromJsonArray( array $mappings ): array {
		$propertyMappings = [];

		foreach ( $mappings as $propertyId => $propertyMapping ) {
			$propertyMappings[] = new PropertyMapping(
				propertyId: $propertyId,
				subfields: $propertyMapping['subfields'] ?? [],
				condition: $this->getSubfieldConditionFromPropertyMappingArray( $propertyMapping )
			);
		}

		return $propertyMappings;
	}

	private function getSubfieldConditionFromPropertyMappingArray( array $propertyMapping ): ?SubfieldCondition {
		if ( array_key_exists( 'conditions', $propertyMapping ) && array_key_exists( 0, $propertyMapping['conditions'] ) ) {
			$conditionArray = $propertyMapping['conditions'][0];
			return new SubfieldCondition( $conditionArray['subfield'], $conditionArray['equalTo'] );
		}

		return null;
	}

	private function propertyDefinitionsFromJsonArray( array $json ): PropertyDefinitionList {
		$properties = [];

		foreach ( $json as $picaField => $mappings ) {
			foreach ( $mappings as $propertyId => $propertyMapping ) {
				if ( array_key_exists( 'type', $propertyMapping ) ) {
					$properties[] = new PropertyDefinition(
						propertyId: $propertyId,
						propertyType: $propertyMapping['type'],
						labels: $propertyMapping['labels'] ?? [],
					);
				}
			}
		}

		return new PropertyDefinitionList( ...$properties );
	}

}
