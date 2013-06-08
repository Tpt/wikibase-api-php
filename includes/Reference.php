<?php

/**
 * A reference.
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
	 * @var array "property id" => Snak[]
	 */
	protected $snaks;

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
			foreach( $data['snaks'] as $prop => $list ) {
				$this->snaks[$prop] = array();
				foreach( $list as $val ) {
					$snak = Snak::newFromArray( $val );
					$this->snaks[$prop][$snak->getDataValue()->getHash()] = $snak;
				}
			}
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
	 * @param Snak[] $snaks
	 * @return Reference
	 * @throws Exception
	 */
	public static function newFromSnaks( Statement $statement, array $snaks ) {
		$snakArray = array();
		foreach( $snaks as $snak ) {
			$snakArray[$snak->getPropertyId()->getPrefixedId()][] = $snak->toArray();
		}
		$reference = self::newFromArray( $statement, $snakArray );
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
	 * @return Statement
	 */
	public function getStatement() {
		return $this->statement;
	}

	/**
	 * @return array "property id" => Snak[]
	 */
	public function getSnaks() {
		return $this->snaks;
	}

	/**
	 * @param Snak $snak
	 */
	public function addSnak( Snak $snak ) {
		if( !isset( $this->snacks[$snak->getPropertyId()->getPrefixedId()] ) ) {
			$this->snacks[$snak->getPropertyId()->getPrefixedId()] = array();
		}
		$this->snacks[$snak->getPropertyId()->getPrefixedId()][$snak->getDataValue()->getHash()] = $snak;
	}

	/**
	 * @param Snak $snak
	 */
	public function removeSnak( Snak $snak ) {
		unset( $this->snacks[$snak->getPropertyId()->getPrefixedId()][$snak->getDataValue()->getHash()] );
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
		$result = $this->statement->getEntity()->getApi()->setReference( $this->statement->getId(), $this->snaks, $this->hash, $this->statement->getEntity()->getLastRevisionId(), $summary );
		$this->updateDataFromResult( $result );
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
