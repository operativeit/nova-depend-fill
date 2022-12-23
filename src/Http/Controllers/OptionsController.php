<?php

namespace EomPlus\Nova\Fields\DependFill\Http\Controllers;

use EomPlus\Nova\Fields\DependFill\DependFill;

use Illuminate\Routing\Controller;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

use Illuminate\Support\Arr;

class OptionsController extends Controller
{
    public static function traverseFields($fields, $pad='') {
        $result = [];
        foreach ($fields as $field) {
            if ($field->component == 'nova-flexible-content') {
                foreach ($field->meta['layouts'] as $layout) {
                    $result[ $layout->name() ] = self::traverseFields($layout->fields(), $pad."\t");
                }
            } else if ($field->component == 'simple-repeatable') {
                $result[ $field->attribute ] = self::traverseFields($field->row()->fields(), $pad."\t");
            } else {
                $result[ $field->attribute ] = $field;
            }
        }
        return $result;
    }

    public function index(NovaRequest $request, $resource, $attribute, $value)
    {
        $resource = $request->newResource();
        $fields = $resource->updateFields($request);
        
        if ($field = $fields->findFieldByAttribute($attribute)) {
            return $field->getValues($value);
        }

        return [];
    }
}
