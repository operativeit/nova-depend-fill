# Nova depend fill
## Installation

```bash
composer require eom-plus/nova-depend-fill
```
## Requirements
- `php: >=8.0`
- `laravel/nova: ^4.0`

## Description

Fill value and/or update options of a Laravel Nova field depends of value of another field.

This custom field is for Nova 4 only and it's based on the following original works:
- [klepak/nova-depend-fill](https://github.com/klepak/nova-depend-fill)
- [alexwenzel/nova-dependency-container](https://github.com/alexwenzel/nova-dependency-container)
- [epartment/nova-dependency-container](https://github.com/epartment/nova-dependency-container)


## Usage

You can fill one or multiple fields. Values are fetch from callback set to method *updateValuesUsing()*. 
You can also using magic methods such *update[Field]ValueUsing()* (eg: updateEmailValueUsing) with individual callback per target field.

You can set select options in the same way using global method *setOptionsUsing()* or magic one such *set[Field]OptionsUsing()* (eg: setPersonOptionsUsing).


### Simple example 

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

### Example with only one target field and magic callback method "updateEmailValueUsing"

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
### Example filling dynamically select list using a callback. 

Options format that must return callback is not exactly the same as Nova documentation, you must specify 'value' key in the list instead use it as main array key

```
    $options = [
        ['label' => 'Person 1', 'value' => 'person1', 'group'=> 'group 1'],
        ['label' => 'Person 2', 'value' => 'person2', 'group'=> 'group 1']
    ]

```
instead of 

```
     $options = [
        'person1' => ['label' => 'Person 1' 'group'=> 'group 1'],
        'person2' => ['label' => 'Person 2', 'group'=> 'group 1']
    ]
```
          

```php
DependFill::make('template')
    ->field(
        Email::make('email'),
    )
    ->dependsOn(
        Select::make('person')
    )
    ->setPersonOptionsUsing(function($value, $model) {
        return [
            ['label' => 'Person 1', 'value' => 'person1', 'group'=> 'group 1'],
            ['label' => 'Person 2', 'value' => 'person2', 'group'=> 'group 1']
        ];
    })
    ->updateEmailValueUsing(function ($value) {
        return $value . '@mydomain.com';
    })    
    ->withMeta(['value' => 'person2'])
])
```

### Known bugs

Fields are not rendered properly on index and details page.

### Future developments
- Add support for relevant (hide/show), readonly, disabled based on dependencies and might on expression
- Might make options array format to match exactly the same as Nova select field
- Support for [flexible content](https://github.com/whitecube/nova-flexible-content) 
- ...
