<?php

namespace EomPlus\Nova\Fields\DependFill\Http\Controllers;

use EomPlus\Nova\Fields\DependFill\DependFill;

use Illuminate\Routing\Controller;

use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

use Illuminate\Support\Arr;

class OptionsController extends Controller
{
    public function values(NovaRequest $request, $resource, $attribute)
    {
        $resource = $request->newResource();
        $fields = $resource->updateFields($request);
        
        if ($field = $fields->findFieldByAttribute($attribute)) {
            return $field->getValues($value);
        }

        return [];
    }

    public function options(NovaRequest $request, $resource, $attribute)
    {
        $resource = $request->newResource();
        $fields = $resource->updateFields($request);

        $origin = $request->get('origin');
        $value = $request->get('value');

        if ($field = $fields->findFieldByAttribute($attribute)) {
            if ($origin && $originField = $fields->findFieldByAttribute($origin)) {

                if ($originField instanceOf \Laravel\Nova\Fields\MorphTo) {
                    $morphToTypes = Arr::keyBy($originField->morphToTypes,'value');

                    list($originKey, $originValue) = $value;
                    $originResource = $morphToTypes[$originKey]['type'];
                    $originModel = $originResource::$model;

                    return $field->getOptions($originValue, $originModel);
        
                } else {
                    return $field->getOptions($value);
                }

            }
        }

        return [];
    }
}