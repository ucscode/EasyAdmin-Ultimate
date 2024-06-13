## Affix Feature in EasyAdminUltimate

The `affix` option in EasyAdminUltimate enhances form fields by allowing you to append or prepend additional content, such as buttons or icons, to the field itself. This feature transforms standard form elements into input groups, providing flexibility in designing user interfaces.

### Usage Example

You can use the `affix` option with form fields to append or prepend content:

```php
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

TextField::new('name')
    ->setFormTypeOption('affix', [
        'append' => [
            'type' => 'button',
            'value' => [
                'label' => 'Click Me',
                'attributes' => [
                    'class' => 'btn btn-secondary',
                    'data-id' => 'btn-id'
                ]
            ]
        ],
        // 'prepend' configuration can also be added similarly
    ]);
```

### Explanation

- **TextField::new('name')**: Defines a new text field in EasyAdminUltimate.
- **setFormTypeOption('affix', [...]**): Sets the `affix` option for the text field.
- **'append'**: Specifies that content should be appended to the text field.
- **'type' => 'button'**: Defines the type of affix as a button.
- **'value'**: Specifies the content of the button.
  - **'label'**: Text displayed on the button ('Click Me' in this case).
  - **'attributes'**: Additional HTML attributes for the button.

### Maximal Configuration

The `affix` option supports different configurations based on the type of content:

- **'type' = 'text'**: Displays any string as appended or prepended content.
- **'type' = 'icon'**: Uses an icon class (e.g., `"fas fa-any-icon"`) for styling.
- **'type' = 'button'**: Adds a button with customizable label and attributes.
  - If `'value'` is an array:
    - **'icon'**: Specifies an icon class instead of a label.
    - **'label'**: Text displayed on the button if no icon is specified.
    - **'attributes'**: Additional HTML attributes for the button.

### Integrating with Simple Widget

When applied to a `simple_widget` field, the `affix` option converts the field into an input group widget with the appended or prepended content. This transformation enhances user interaction by embedding interactive elements directly within form fields.

### Conclusion

The `affix` feature in EasyAdminUltimate provides powerful customization options for form fields, allowing you to create input groups with appended or prepended content such as buttons or icons. This flexibility enhances usability and user experience across your Symfony applications.

For more detailed configuration options and examples, please refer to the EasyAdminUltimate documentation and internal source code.