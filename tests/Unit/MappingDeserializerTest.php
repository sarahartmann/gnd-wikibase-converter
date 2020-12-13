<?php

declare( strict_types = 1 );

namespace DNB\Tests\Unit;

use DNB\WikibaseConverter\MappingDeserializer;
use DNB\WikibaseConverter\PropertyDefinition;
use DNB\WikibaseConverter\PropertyDefinitionList;
use DNB\WikibaseConverter\PropertyMapping;
use DNB\WikibaseConverter\SubfieldCondition;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DNB\WikibaseConverter\MappingDeserializer
 * @covers \DNB\WikibaseConverter\Mapping
 * @covers \DNB\WikibaseConverter\PropertyMapping
 */
class MappingDeserializerTest extends TestCase {

	public function testSimplePropertyMapping() {
		$mapping = ( new MappingDeserializer() )->jsonArrayToObject( [
			'029A' => [
				'P3' => [
					'subfields' => [ 'b' ]
				]
			]
		] );

		$this->assertEmpty( $mapping->getPropertyMappings( '404' ) );

		$this->assertEquals(
			[
				new PropertyMapping(
					propertyId: 'P3',
					subfields: [ 'b' ]
				)
			],
			$mapping->getPropertyMappings( '029A' )
		);
	}

	public function testPropertyMappingWithCondition() {
		$mapping = ( new MappingDeserializer() )->jsonArrayToObject( [
			'007K' => [
				'P2' => [
					'subfields' => [ '0' ],
					'conditions' => [
						[
							'subfield' => 'a',
							'equalTo' => 'gnd',
						]
					],
				]
			]
		] );

		$this->assertEquals(
			[
				new PropertyMapping(
					propertyId: 'P2',
					subfields: [ '0' ],
					condition: new SubfieldCondition( 'a', 'gnd' )
				)
			],
			$mapping->getPropertyMappings( '007K' )
		);
	}

	public function testSimplePropertyDefinition() {
		$mapping = ( new MappingDeserializer() )->jsonArrayToObject( [
			'P1C4' => [
				'P1' => [
					'type' => 'string'
				],
			]
		] );

		$this->assertEquals(
			new PropertyDefinitionList(
				new PropertyDefinition( 'P1', 'string' )
			),
			$mapping->getProperties()
		);
	}

	public function testMultiplePropertyDefinitions() {
		$mapping = ( new MappingDeserializer() )->jsonArrayToObject( [
			'P1C4' => [
				'P1' => [
					'type' => 'string'
				],
				'P2' => [
					'type' => 'string'
				],
			],
			'M0R3' => [
				'P3' => [
					'type' => 'string'
				],
			]
		] );

		$this->assertEquals(
			new PropertyDefinitionList(
				new PropertyDefinition( 'P1', 'string' ),
				new PropertyDefinition( 'P2', 'string' ),
				new PropertyDefinition( 'P3', 'string' ),
			),
			$mapping->getProperties()
		);
	}

	public function testLabels() {
		$mapping = ( new MappingDeserializer() )->jsonArrayToObject( [
			'P1C4' => [
				'P1' => [
					'type' => 'string',
					'labels' => [ 'en' => 'English', 'de' => 'German' ]
				],
			]
		] );

		$this->assertEquals(
			new PropertyDefinitionList(
				new PropertyDefinition(
					'P1',
					'string',
					labels: [ 'en' => 'English', 'de' => 'German' ]
				)
			),
			$mapping->getProperties()
		);
	}

}
