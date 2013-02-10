<?php

/**
 * A claim.
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
class Claim {

	/**
	 * @var Entity
	 */
	protected $entity;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var Snak
	 */
	protected $mainSnak;

	/**
	 * @var Snak[]
	 */
	protected $qualifiers = array();

	/**
	 * @protected
	 * @param Entity $entity
	 * @param array $data
	 */
	public function __construct( Entity $entity, array $data ) {
		$this->entity = $entity;
		$this->fillData( $data );
	}

	protected function fillData( array $data ) {
		if( isset( $data['id'] ) ) {
			$this->id = $data['id'];
		}
		if( isset( $data['mainsnak'] ) ) {
			$this->mainSnak = Snak::newFromArray( $this, $data['mainsnak'] );
		}
	}

	/**
	 * @param Entity $entity
	 * @param Snak $snak
	 * @return Claim
	 */
	public function newFromSnak( WikibaseApi $api, Snak $snak ) {
		$claim = new self( $entity, array() );
		$claim->setMainSnak( $snak );
		return $claim;
	}

	/**
	 * @param Entity $entity
	 * @param array $data
	 * @return Claim
	 * @throws Exception
	 */
	public function newFromArray( Entity $entity, array $data ) {
		if( isset( $data['type'] ) ) {
			switch( $data['type'] ) {
				case 'statement':
					return new Statement( $entity, $data );
				default:
					return new self( $entity, $data );
			}
		}
		throw new Exception( 'Unknown type!' );
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return Snak
	 */
	public function getMainSnak() {
		return $this->mainSnak;
	}
}
