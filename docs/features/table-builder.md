Sure, here is a professional and descriptive documentation about the service information you provided:

# TableBuilder Documentation

## Introduction

EasyAdmin does not provide a method of building custom tables except through the use of CRUD which is generated automatically by EasyAdmin. EasyAdmin Ultimate provides a solution to create exactly the same table similar to CRUD by providing a powerful `TableBuilder` class.

The `TableBuilder` is a utility that utilizes a `Cell` object which represents an intersection between a column and a row. There are two types of objects that are subclasses of the `Cell` object:

1. `ColumnCell`
2. `DataCell`

The `ColumnCell` is a cell unit representing the configuration in the `thead` section while the `DataCell` is a cell unit representing each `td` configuration in the `tbody` section.

## Table Builder Usage

```php
use App\Utility\TableBuilder;
// optionally
use App\Utility\ColumnCell;
use App\Utility\DataCell;

$table = new TableBuilder('table-name');
```

### Setting/Getting Table Columns

```php
$table->setColumns([
    new ColumnCell('name'),
    new ColumnCell('email'),
    'Date' // This will internally be converted to ColumnCell
]);

$table->getColumns(); // returns an array of ColumnCells
```

The above represents a way of adding and getting all columns. To set rows for each column, you should define the rows as an array of arrays. The inner array should contain strings or `DataCells`.

### Setting/Getting Table Rows

```php
// String will internally be converted to DataCell instance

$table->setRows(array(
  array(
    'john', 
    'john@email.com', 
    'may-23-2003'
  ),
  array(
    new DataCell('mary'), 
    new DataCell('mary@email.com'), 
    new DataCell('jun-14-1998')
  ),
  array(
    'Sintia', 
    new DataCell('sintia@email.com'), 
    'jan-03-2000'
  ),
  ...
));

$table->getRows(); // will return only array of arrays of DataCell (not strings even if strings were set as part of the data)
```

### Removing Table Rows

To remove a row, you must pass a callable and if the callable returns `true`, the row will be removed. Example:

```php
$table->removeRows(function(array $row) {
    $cell = $row[0];
     return $cell->getValue() == 'mary';
});
```

This will filter the rows and remove the second one which has a value of `mary`.

### Adding Checkboxes

To automatically add checkboxes at the start of each row where you can select all or individual row:

```php
$table->setBatchActions(true, 1);
```

- The first parameter (required) indicates a batch action and will add a checkout to the beginning of all fields if set to `true`. 
- The second parameter (required) called `associateIndex` indicates the column whose value will be used as the value of the checkbox field. 

For example, the value `1` means that the `email` column will be used as the batch action value. Therefore, if the table was embedded within a form and submitted, the submitted data will contain all emails in the selected row.  
Likewise, if the `associateIndex` is `0`, then the submitted data will contain all `names` of selected rows

### Pagination Settings

The `TableBuilder` also contains a `Paginator` that provides a nav for viewing table data sequentially. To apply custom configuration to the `Paginator`, you can access it and configure it.

```php
$table->getPaginator()
  ->setItemsPerPage(20) // 20 rows per page
  ->setUrlPattern('domain.com/page=' . Paginator::NUM_PLACEHOLDER) // The url pattern to different pages
  ->setCurrentPage(1)
  ...
```

The `Paginator` library is a standalone library forked from `jasongrimes/php-paginator`. This forked version provides additional functionality and modern PHP compatibility. Explore the `Paginator` at [https://github.com/ucscode/php-paginator](https://github.com/ucscode/php-paginator).

### Cell Methods

The `DataCell` and `ColumnCell` both extend the `Cell` class. This cell provides the following methods:

Sure, here's the information in a listed markdown format:

#### `TableBuilder::setValue()`:

This method is used to assign a specific value to a cell. The value you set using this method is what will be displayed inside the table cell when it is rendered. For example: 

```php
$cell->setValue('John');
```

#### `TableBuilder::getValue()`:

This method is used to retrieve the value that was previously set using `setValue`. When you call this method, it returns the current value of the cell. 

#### `TableBuilder::setAttributes()`:

This method is used to set an array that will be converted to HTML attributes for the cell. For example, 

```php
$cell->setAttributes(['data-code' => 123]);
``` 

will render as `<td data-code="123">...</td>` for the particular table cell, the node name depends on whether it's a `ColumnCell` or `DataCell`.

#### `TableBuilder::getAttributes()`:

This method is used to retrieve the attributes that were previously set using `setAttributes`.

#### `TableBuilder::setHidden()`:

This method is used to set whether the cell should be hidden or not. For example, if you want a cell not to be part of the table based on some condition, you can easily check 

```php
if($cell->value() == 'password') {
    $cell->setHidden(true);
}
```

#### `TableBuilder::isHidden()`:

This method is used to check if the cell is hidden. For example: `$isHidden = $cell->isHidden();`

#### `TableBuilder::setMeta()`:

This method is used to set a custom data passed to the cell for custom references, actions, or purposes. This is used to add extra information about the cell that may be used later in the future (especially in the configurator). For example: 

```php
$cell->setMeta('hide-later', true);
```

#### `TableBuilder::getMeta()`:

This method is used to retrieve the meta data that was previously set using `setMeta`. For example: 

```php
$meta = $cell->getMeta();
```

## Configurator

The `Configurator` is a vital part of the `TableBuilder` that give you an outstanding means of rendering your table professionally. Configurators are callables that are called for each cell just before rendering, allowing you to make adequate modifications to each cell property. A `TableBuilder` can have more than one configurator and each will be called sequentially to configure the cell before it is rendered.

### Setting a configurator

```php
$table->setConfigurator('unique-name', function(Cell $cell, int $offset, ?ColumnCell $column) {
  // your configuration here
});
```

The configurator callable receives three arguments:

- `$cell`: This is the `ColumnCell` or `DataCell` with information to be rendered.
- `$offset`: The column index (0 for the first column, 1 for the second, etc.).
- `$column`: The `ColumnCell `corresponding to the current column index (`$offset`), or `null` if no column is defined for that index.


### Removing a configurator

```php
$table->removeConfigurator('unique-name');
```

---

## Feature Hint

If you want your data to render as an anchor (link) rather than just regular text, you can set the "anchor" meta key (only applicable to `DataCell`).

```php
$cell->setMeta('anchor', [
  'href' => 'http://...',
  'target' => '_blank',
  'class' => 'btn btn-primary'
]);
```

If the value of the cell is "john", setting the anchor meta for the cell will render it as 

```html
<a href='http://...' target='_blank' class='btn btn-primary'>
    John
</a>
```

## Rendering the Table

After configuring the `TableBuilder`, it will not automatically be rendered. You'll need to include the `utility/table.html.twig` into your template and pass the table instance. For example:

```php
$this->render('my-template.html.twig', [
  'table_instance' => $table  
]);
```

Then within your template:

```twig
{% include 'utlitiy/table.html.twig' with {table: table_instance} %}
```

This documentation covers the core features and usage of the TableBuilder class in EasyAdmin Ultimate. Use it to build powerful, customizable tables tailored to your application's needs.  
Reference the source code for indepth detail