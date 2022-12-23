<?php

namespace EomPlus\Nova\Fields\DependFill;

use Laravel\Nova\Fields\Field;
use Illuminate\Support\Str;

class DependFill extends Field
{
    protected $updateCallback;
    protected $updateCallbacks = [];

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'eom-depend-fill';

    /**
     * Set the field
     *
     * @param  Field  $field
     * @return $this
     */
    public function field($field)
    {
        return $this->withMeta(['fields' => [ $field ]]);
    }

    /**
     * Set the fields
     *
     * @param  Array $fields
     * @return $this
     */
    public function fields($fields)
    {
        return $this->withMeta(['fields' => $fields]); 
    }


    /**
     * Set the dependsOn
     *
     * @param  Field  $dependsOn
     * @return $this
     */
    public function dependsOn($dependsOn)
    {
        return $this->withMeta(['dependsOn' => $dependsOn]);
    }

    public function options($options)
    {
        $this->options = $options;
        return $this;
    }

    public function getValues($attributes)
    {
        $values = is_callable($this->updateCallback)?call_user_func($this->updateCallback, $attributes): [];

        foreach ($this->updateCallbacks as $updateCallbackKey => $updateCallback) {
            $values[$updateCallbackKey] = (is_callable($updateCallback)?call_user_func($updateCallback, $attributes): []);    
        }
     
        \Log::info(print_r($values,true));
        return $values;
    }

    /**
     * Set the values
     *
     * @param  array  $values
     * @return $this
     */
    public function values(array $values)
    {
        return $this->withMeta(['values' => $values]);
    }

    public function updateValuesUsing(callable $callback) {
        $this->updateCallback = $callback;
        return $this;
    }

    public function __call( $method, $arguments) {

        if (preg_match('/update(.+)ValueUsing/', $method, $matches)) {
            $key = Str::of($matches[1])->trim()->snake()->__toString();
            $callback = $arguments[0];
            
            $this->updateCallbacks[$key] = $callback;
        }
        
        return $this;
    }
}
