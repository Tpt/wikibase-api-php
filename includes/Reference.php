<?php

/**
* A statement.
*
* This program is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License along
* with this program; if not, write to the Free Software Foundation, Inc.,
* 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
* http://www.gnu.org/copyleft/gpl.html
*
* @file
*
* @licence GNU GPL v2+
* @author Benestar
*/
class Reference {

	/**
	 * @var Statement
	 */
	protected $statement;

	/**
	 * @var Snak
	 */
	protected $snak;

	/**
	 * @var string
	 */
	protected $hash;

	/**
	 * @var string
	 */
	protected $internalId;

	public function __construct( Statement $statement, array $data ) {
		$this->statement = $statement;
		$this->fillData( $data );
	}

	protected function fillData( array $data ) {
		if( isset( $data['snaks'] ) ) {
			$snaks = reset( $data['snaks'] );
			$this->snak = Snak::newFromArray( $snaks[0] );
		}
		if( isset( $data['hash'] ) ) {
			$this->hash = $data['hash'];
		}
		if( $this->internalId === null ) {
			if( $this->hash !== null ) {
				$this->internalId = $this->hash;
			}
			else {
				$this->internalId = time() . $this->statement->getInternalId(); //TODO improve
			}
		}
	}
	
	/**
	 * @param Statement $statement
	 * @param Snak $snak snak to be used as main snak
	 * @return Reference
	 * @throws Exception
	 */
	public static function newFromSnak( Statement $statement, Snak $snak ) {
		$reference = self::newFromArray( $statement, array( 'snaks' => self::getSnakArray( $snak ) ) );
		$statement->addReference( $reference );
		return $reference;
	}
	
	/**
	 * @param Statement $statement
	 * @param array $data
	 * @return Reference
	 * @throws Exception
	 */
	public static function newFromArray( Statement $statement, array $data ) {
		return new self( $statement, $data );
	}

	/**
	 * @return string
	 */
	public function getInternalId() {
		return $this->internalId;
	}

	/**
	 * Save the reference and pusch the change to the database
	 *
	 * @param string $summary summary for the change
	 * @throws Exception
	 */
	public function save( $summary = '' ) {
		$id = $this->statement->getId();
		if( $id === null ) {
			throw new Exception( 'Statement has no Id. Please save the statement first.' );
		}
		$snakArray = json_encode( self::getSnakArray( $this->snak ) );
		$result = $this->statement->getEntity()->getApi()->setReference( $this->statement->getId(), $snakArray, $this->hash, $this->statement->getEntity()->getLastRevisionId(), $summary );
		$this->updateDataFromResult( $result );
	}

	/**
	 * @param Snak $snak
	 * @return array
	 */
	protected static function getSnakArray( Snak $snak ) {
		return array( $snak->getPropertyId()->getPrefixedId() => array( $snak->toArray() ) );
	}

	/**
	 * Update data from the result of an API call
	 */
	protected function updateDataFromResult( $result ) {
		if( isset( $result['reference'] ) ) {
			$this->fillData( $result['reference'] );
		}
		if( isset( $result['pageinfo']['lastrevid'] ) ) {
			$this->statement->getEntity()->setLastRevisionId( $result['pageinfo']['lastrevid'] );
		}
	}

	/**
	 * Delete the reference and push the change to the database
	 *
	 * @param string $summary summary for the change
	 * @throws Exception
	 */
	public function deleteAndSave( $summary = '' ) {
		$id = $this->statement->getId();
		if( $id === null ) {
			throw new Exception( 'Statement has no Id. Please save the statement first.' );
		}
		if( $this->hash !== null ) {
			$this->statement->getEntity()->getApi()->removeReferences( $id, array( $this->hash ), $this->statement->getEntity()->getLastRevisionId(), $summary );
		}
		$this->statement->removeReference( $this );
	}
}
