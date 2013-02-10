<?php

/**
 * A snak.
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
 *
 * @todo datavalue managment
 */
class Snak {

	/**
	 * @var string
	 */
	protected $type;

	/**
	 * @var string
	 */
	protected $propertyId;

	/**
	 * @var mixed
	 */
	protected $datavalue;

	/**
	 * @protected
	 * @param Claim $claim
	 * @param array $data
	 */
	public function __construct( Claim $claim, array $data ) {
		$this->claim = $claim;
		$this->fillData( $data );
	}

	protected function fillData( array $data ) {
		if( isset( $data['snaktype'] ) ) {
			$this->type = $data['snaktype'];
		}
		if( isset( $data['property'] ) ) {
			$this->propertyId = $data['property'];
		}
		if( isset( $data['datavalue'] ) ) {
			$this->datavalue = $data['datavalue'];
		}
	}

	/**
	 * @param Claim $claim
	 * @param array $data
	 * @return Snak
	 * @throws Exception
	 */
	public function newFromArray( Claim $claim, array $data ) {
		return new Snak( $claim, $data );
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getPropertyId() {
		return $this->propertyId;
	}
}
