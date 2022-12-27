<?php

namespace EomPlus\Nova\Fields\DependFill;

use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Str;

class DependFill extends Field
{
    protected $updateCallback;
    protected $updateCallbacks = [];

    protected $optionsCallback;
    protected $optionsCallbacks = [];

    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'eom-depend-fill';

    /**
     * NovaDependencyContainer constructor.
     *
     * @param $fields
     * @param null $attribute
     * @param null $resolveCallback
     */
    public function __construct($attribute, $fields = [])
    {
        parent::__construct('', $attribute);
        $this->withMeta(['fields' => $fields]);
        $this->withMeta(['dependencies' => []]);
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
    public function dependsOn($field, $value = null)
    {
        return $this->withMeta([
            'dependencies' => array_merge($this->meta['dependencies'], [
                $field
            ])
        ]);
    }

    public function options($options)
    {
        $this->options = $options;
        return $this;
    }

    public function getOptions($value, $model = null)
    {
        $options = is_callable($this->optionsCallback)?call_user_func($this->optionsCallback, $value, $model): [];

        foreach ($this->optionsCallbacks as $optionsCallbackKey => $optionsCallback) {
            $options[$optionsCallbackKey] = (is_callable($optionsCallback)?call_user_func($optionsCallback, $value, $model): []);    
        }

        return $options;
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
     * Resolve dependency fields
     *
     * @param mixed $resource
     * @param string $attribute
     * @return array|mixed
     */
    public function resolve($resource, $attribute = null)
    {
        foreach ($this->meta['fields'] as $field) {
            $field->resolve($resource, $attribute);
        }
      
    }

    /**
     * Forward fillInto request for each field in this container
     *
     * @trace fill/fillForAction -> fillInto -> *
     *
     * @param NovaRequest $request
     * @param $model
     * @param $attribute
     * @param null $requestAttribute
     */
    public function fillInto(NovaRequest $request, $model, $attribute, $requestAttribute = null)
    {
        $callbacks = [];
        
        \Log::info($this->meta['fields']);

        foreach ($this->meta['fields'] as $field) {
            /** @var Field $field */
            $callbacks[] = $field->fill($request, $model);
            \Log::info($field->attribute);
            //\Log::info($model->{$field->attribute});
        }

        return function () use ($callbacks) {
            foreach ($callbacks as $callback) {
                if (is_callable($callback)) {
                    call_user_func($callback);
                }
            }
        };
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

    public function setOptionsUsing(callable $callback) {
        $this->optionsCallback = $callback;
        return $this;
    }

    public function __call( $method, $arguments) {

        if (preg_match('/update(.+)ValueUsing/', $method, $matches)) {
            $key = Str::of($matches[1])->trim()->snake()->__toString();
            $callback = $arguments[0];
            
            $this->updateCallbacks[$key] = $callback;
        }
        
        if (preg_match('/set(.+)OptionsUsing/', $method, $matches)) {
            $key = Str::of($matches[1])->trim()->snake()->__toString();
            $callback = $arguments[0];
            
            $this->optionsCallbacks[$key] = $callback;
        }

        return $this;
    }
}
