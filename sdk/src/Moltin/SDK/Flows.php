<?php

/**
* This file is part of Moltin PHP-SDK, a PHP package which
* provides convinient and rapid access to the API.
*
* Copyright (c) 2013 Moltin Ltd.
* http://github.com/moltin/php-sdk
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*
* @package moltin/php-sdk
* @author Jamie Holdroyd <jamie@molt.in>
* @copyright 2013 Moltin Ltd.
* @version dev
* @link http://github.com/moltin/php-sdk
*
*/

namespace Moltin\SDK;

use Moltin\SDK\Exception\InvalidFieldTypeException as InvalidFieldType;

class Flows
{
	protected $fields;
	protected $wrap;
	protected $args;

	public function __construct($fields, $wrap = false)
	{
		$this->fields = $fields;
		$this->wrap   = $wrap;
	}

	public function build()
	{
		// Loop fields
		foreach ( $this->fields as &$field ) {

			// Variables
			$method = 'type'.str_replace(' ', '', ucwords(str_replace('-', ' ', $field['type'])));

			// Check for method
			if ( method_exists($this, $method) ) {

				// Setup args
				$this->args = array(
					'name'     => $field['slug'],
					'id'       => $field['slug'],
					'value'    => ( isset($_POST[$field['slug']]) ? $_POST[$field['slug']] : ( isset($field['value']) ? $field['value'] : null ) ),
					'required' => ( $field['required'] == 1 ? 'required' : false ),
					'class'    => ['form-control']
				);

				// Wrap form value
				if ( isset($this->wrap) && $this->wrap !== false ) { $this->args['name'] = $this->wrap.'['.$field['slug'].']'; }

				// Build input
				$field['input'] = $this->$method($field);

			// Not found
			} else {
				throw new InvalidFieldType('Field type '.$field['type'].' was not found');
			}
		}

		return $this->fields;
	}

	protected function typeString($a)
	{
		$this->args['type'] = 'text';
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeDate($a)
	{
		$this->args['type'] = 'text';
		$this->args['class'][] = 'datepicker';

		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeEmail($a)
	{
		$this->args['type'] = 'email';
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeSlug($a)
	{
		$this->args['type']        = 'text';
		$this->args['class'][]     = 'slug';
		$this->args['data-parent'] = '#'.$a['options']['parent'];
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeInteger($a)
	{
		$this->args['type'] = 'text';
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeDecimal($a)
	{
		$this->args['type']        = 'text';
		$this->args['class'][]     = 'decimal';
		$this->args['data-places'] = $a['options']['decimal_places'];
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeChoice($a)
	{
		if ( is_array($this->args['value']) ) { $this->args['value'] = $this->args['value']['data']['key']; }
		$options = $this->_buildOptions($a['options']['choices'], $a['name'], $this->args['value'], $a['options']['default'], $a['required']);
		return '<select '.$this->_buildArgs($this->args, true).'>'.$options.'</select>';
	}

	protected function typeRelationship($a)
	{
		if ( is_array($this->args['value']) && isset($this->args['value']['data']['id'])) { $this->args['value'] = $this->args['value']['data']['id']; }
		$options = $this->_buildOptions(( isset($a['available']) ? $a['available'] : null ), $a['name'], $this->args['value'], null, $a['required']);
		return '<select '.$this->_buildArgs($this->args, true).'>'.$options.'</select>';
	}

	protected function typeMultiple($a)
	{
		if ( ! isset($_POST[$this->args['name']]) && is_array($this->args['value']) ) { $this->args['value'] = array_keys($this->args['value']['data']); }
		$this->args['multiple'] = 'multiple';
		$this->args['name']    .= '[]';
		return $this->typeRelationship($a);
	}

	protected function typeTaxBand($a)
	{
		return $this->typeRelationship($a);
	}

	protected function typeCountry($a)
	{
		return $this->typeRelationship($a);
	}

	protected function typeCurrency($a)
	{
		return $this->typeRelationship($a);
	}

	protected function typeGateway($a)
	{
		return $this->typeRelationship($a);
	}

	protected function typeText($a)
	{
		$value = $this->args['value'];
		unset($this->args['value']);
		return '<textarea '.$this->_buildArgs($this->args).'>'.$value.'</textarea>';
	}

	protected function _buildArgs($args, $skipValue = false)
	{
		$string = '';
		foreach ( $args as $key => $value ) {
			if ($key != "value" or ! $skipValue) {
				if (! empty($value) ) {
					$string .= $key.'="'.( is_array($value) ? implode(' ', $value) : $value ).'" ';
				} elseif ($key != "required" && ! empty($value) ) {
					$string .= $key.' ';
				}
			}
		}
		return trim($string);
	}

	protected function _buildOptions($options, $title, $value = null, $default = null, $required = false)
	{
		$string = ( ! $required ? '<option value="">Select a '.$title.'</option>' : '' );
		
		if ( $options !== null ) {
			foreach ( $options as $id => $title ) { $string .= '<option value="'.$id.'"'.( ( is_array($value) && in_array($id, $value) ) || ( isset($value['data']) && (is_array($value) && in_array($id, $value['data'])) ) || $value == $id || ( $value == null && $default == $id ) ? ' selected="selected"' : '' ).'>'.$title.'</option>'; }
		}

		return $string;
	}
}
