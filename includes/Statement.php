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
	 * @var Reference[]
	 */
	protected $references = array();

	protected function fillData( array $data ) {
		parent::fillData( $data );
		if( isset( $data['rank'] ) ) {
			$this->rank = $data['rank'];
		}
		if( isset( $data['references'] ) ) {
			foreach( $data['references'] as $reference ) {
				$this->references[] = Reference::newFromArray( $this, $reference );
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
	 * @return References[]
	 */
	public function getReferences() {
		return $this->references;
	}

	/**
	 * @param Reference $reference
	 */
	public function addReference( Reference $reference ) {
		$this->references[$reference->getInternalId()] = $reference;
	}

	/**
	 * @param Reference $reference
	 */
	public function removeReference( Reference $reference ) {
		unset( $this->references[$reference->getInternalId()] );
	}

	/**
	 * @param Snak $snak
	 * @throws Exception
	 */
	public function createReferenceForSnak( Snak $snak ) {
		return Reference::newFromSnak( $this, $snak );
	}
}
