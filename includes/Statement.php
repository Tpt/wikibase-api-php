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
 * @author Thomas Pellissier Tanon
 */
class Statement extends Claim {

	/**
	 * @var string
	 */
	protected $rank = 'normal';

	/**
	 * @var Snak[]
	 */
	protected $references = array();

	protected function fillData( array $data ) {
		parent::fillData( $data );
		if( isset( $data['rank'] ) ) {
			$this->rank = $data['rank'];
		}
		if( isset( $data['references'] ) ) {
			foreach( $data['references'] as $reference ) {
				$snaks = reset( $reference['snaks'] );
				$this->references[] = Snak::newFromArray( $snaks[0] );
			}
		}
	}

	/**
	 * @return string
	 */
	public function getRank() {
		return $this->rank;
	}

	/**
	 * @return array
	 */
	public function getReferences() {
		return $this->references;
	}

	/**
	 * @param Snak[] $snaks the array of Snaks
	 * @param string $reference a hash of the reference that should be updated. When not provided, a new reference is created
	 * @param string $summary summary for the change
	 */
	public function setReferences( array $snaks, $reference = null, $summary = '' ) {
		if( $this->id === null ) {
			throw new Exception( 'No id available' );
		}
		else {
			$snakArray = array();
			foreach( $snaks as $snak ) {
				$snakArray[$snak->getPropertyId()->getPrefixedId()] = $snak->toArray();
			}
			$result = $this->entity->getApi()->setReference( $this->id, json_encode( $snakArray ), $reference, $this->entity->getLastRevisionId(), $summary );
			$this->updateDataFromResult( $result );
		}
	}

	/**
	 * Update data from the result of an API call
	 */
	protected function updateDataFromResult( $result ) {
		parent::updateDataFromResult( $result );
		if( isset( $result['reference'] ) ) {
			$this->fillData( $result )
		}
	}
}
