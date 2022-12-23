# Installation

```bash
composer require eom-plus/nova-depend-fill
```

# Description

Fill Laravel Nova field based on value from other field.

This custom field is for Nova 4 only and it's based on the original work
https://github.com/klepak/nova-depend-fill/

You can fill one or multiple fields. Values are fetch from callback set to method *updateValuesUsing()*. 
You can also using magic methods such *update[Field]ValueUsing()* (eg: updateEmailValueUsing) with individual callback per target field.


# Usage

```php
DependFill::make('template')
    ->fields([
        Email::make('email'),
        Text::make('name'),                        
    ])
    ->dependsOn(
        Select::make('person')
            ->options([
                'person1' => 'Person 1',
                'person2' => 'Person 2'
            ])
    )
    ->updateValuesUsing(function ($value) {
        return [
            'name' => Str::upper($value),
            'email' => $value '@mydomain.local',
        ];
    })
    ->withMeta(['value' => 'person2'])
])

```

Example with only one target field and magic callback method "updateEmailValueUsing"

```php
DependFill::make('template')
    ->field(
        Email::make('email'),
    )
    ->dependsOn(
        Select::make('person')
            ->options([
                'person1' => 'Person 1',
                'person2' => 'Person 2'
            ])
    )
    ->updateEmailValueUsing(function ($value) {
        return $value . '@mydomain.com';
    })    
    ->withMeta(['value' => 'person2'])
])
```
