## Overriding Templates

Following Symfony's mechanism to [override templates from bundles](https://symfony.com/doc/current/bundles/override.html#templates),  
you must create the `templates/bundles/EasyAdminBundle/` directory in your application  
and then create new templates with the same path as the original templates. For example:

```
your-project/
├─ ...
└─ templates/
   └─ bundles/
      └─ EasyAdminBundle/
         ├─ layout.html.twig
         ├─ menu.html.twig
         ├─ crud/
         │  ├─ index.html.twig
         │  ├─ detail.html.twig
         │  └─ field/
         │     ├─ country.html.twig
         │     └─ text.html.twig
         ├─ label/
         │  └─ null.html.twig
         └─ page/
            ├─ content.html.twig
            └─ login.html.twig
```

The original templates of the `@EasyAdminBundle` structure described above can be found within the `vendor/` directory at:

```
easycorp/easyadmin-bundle/src/Resources/views/
```

---

Instead of creating the new templates from scratch, you can extend from the original templates and change only the parts you want to override.  
However, you must use a special syntax inside extends to avoid an infinite loop:

```twig
{# templates/bundles/EasyAdminBundle/layout.html.twig #}

{# DON'T DO THIS: it will cause an infinite loop #}
{% extends '@EasyAdmin/layout.html.twig' %}

{# DO THIS: the '!' symbol tells Symfony to extend from the original template #}
{% extends '@!EasyAdmin/layout.html.twig' %}

{# Since you installed the EasyAdmin PowerPack, you can also do this #}
{% extends eau.templatePath('layout', true) %}

{% block sidebar %}
    {# ... #}
{% endblock %}
```

```twig
{# DON'T DO THIS: it will cause infinite loop #}
{% extends '@EasyAdmin/page/content.html.twig' %}

{# DO THIS: #}
{% extends '@!EasyAdmin/page/content.html.twig' %}

{# Since you installed the EasyAdmin PowerPack, you can also do this #}
{% extends eau.templatePath('page/content', true) %}
```
---

More info @ [Easyadmin Design](https://symfony.com/bundles/EasyAdminBundle/current/design.html)