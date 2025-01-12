<?php

declare( strict_types = 1 );

namespace DNB\Tests\Unit;

use DNB\WikibaseConverter\GndItem;
use DNB\WikibaseConverter\GndStatement;
use PHPUnit\Framework\TestCase;

/**
 * @covers \DNB\WikibaseConverter\GndItem
 * @covers \DNB\WikibaseConverter\GndStatement
 * @covers \DNB\WikibaseConverter\GndQualifier
 */
class GndItemTest extends TestCase {

	public function testEmptyRecordHasNoPropertyIds(): void {
		$item = new GndItem();
		$this->assertSame( [], $item->getPropertyIds() );
	}

	public function testEmptyRecordHasNoValuesForProperty(): void {
		$item = new GndItem();
		$this->assertSame( [], $item->getMainValuesForProperty( 'P1' ) );
	}

	public function testCanAddAndGetPropertyValues(): void {
		$item = new GndItem(
			new GndStatement( 'P1', 'foo' ),
			new GndStatement( 'P1', 'bar' )
		);

		$this->assertSame(
			[ 'foo', 'bar' ],
			$item->getMainValuesForProperty( 'P1' )
		);
	}

	public function testValuesOfOtherPropertiesAreNotReturned(): void {
		$item = new GndItem(
			new GndStatement( 'P2', 'foo' ),
			new GndStatement( 'P2', 'bar' )
		);

		$this->assertSame( [], $item->getMainValuesForProperty( 'P1' ) );
	}

	public function testGetStatementsForProperty(): void {
		$item = new GndItem(
			new GndStatement( 'P1', 'foo' ),
			new GndStatement( 'P2', 'nope' ),
			new GndStatement( 'P1', 'bar' )
		);

		$this->assertEquals(
			[
				new GndStatement( 'P1', 'foo' ),
				new GndStatement( 'P1', 'bar' )
			],
			$item->getStatementsForProperty( 'P1' )
		);
	}

	public function testAddFunctions(): void {
		$item = new GndItem();
		$item->addGndStatement( new GndStatement( 'P1', 'foo' ) );
		$item->addGndStatements( [
			new GndStatement( 'P2', 'nope' ),
			new GndStatement( 'P1', 'bar' )
		] );

		$this->assertEquals(
			[
				new GndStatement( 'P1', 'foo' ),
				new GndStatement( 'P1', 'bar' )
			],
			$item->getStatementsForProperty( 'P1' )
		);
	}

	public function testGetStatementsForNonExistingProperty(): void {
		$this->assertSame(
			[],
			( new GndItem() )->getStatementsForProperty( 'P404' )
		);
	}

	public function testGetNumericIdReturnsNullWhenThereIsNoIdStatement(): void {
		$item = new GndItem(
			new GndStatement( 'P1', 'foo' ),
			new GndStatement( 'P2', 'bar' )
		);

		$this->assertNull( $item->getNumericId() );
	}

	public function testGetNumericIdReturnsId(): void {
		$item = new GndItem(
			new GndStatement( 'P1', 'foo' ),
			new GndStatement( GndItem::GND_ID, '123X' ),
			new GndStatement( 'P2', 'bar' )
		);

		$this->assertSame( 1231, $item->getNumericId() );
	}

}
