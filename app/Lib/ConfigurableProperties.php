<?php

namespace App\Lib;

trait ConfigurableProperties
{

    public function __construct(array $properties = [])
    {
        if (!empty($properties)) {
            $this->setProperties($properties);
        }
    }
    
	/**
	 * Try to set properties over setter if available
	 * @param array $properties
	 * @return $this
	 */
	public function setProperties(array $properties)
	{
		$methods = get_class_methods($this);
		foreach ($properties as $key => $value) {
			$method = 'set' . ucfirst(\Str::camel($key));
			if (in_array($method, $methods)) {
				$this->$method($value);
			}
		}
		
		return $this;
	}
	
	/**
	 * Returns array of properties with snake case keys.
	 * Only properties that have getter are returned
	 * @return array
	 */
	public function getProperties()
	{
        $properties = array();
        
        $objectProperties = array_keys(get_object_vars($this));
		
		foreach ($objectProperties as $objectProperty) {
			$method = 'get' . ucfirst(\Str::camel($objectProperty));
			if (method_exists($this, $method)) {
				$properties[\Str::snake($objectProperty)] = $this->$method();
			}
		}
		
		return $properties;
    }
}