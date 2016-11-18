Readme
===
PHP Script to convert XML to CSV

Not very useful at the moment, because it only exports from this format:

    <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
    <issues>
        <issue id="id1">
            <field name="fieldName1">
                <value>value1</value>
            </field>
            <field name="fieldName2">
                <value>value2</value>
            </field>
        </issue>
    </issues>

From File
---
```
php convert.php input_file.xml output_file.csv
```

From Youtrack API
---
```
php convertFromYoutrackApi.php
```

You must have a configuration file called youtrack.config in this format:


    {
        "authUrl": "https://example.com/hub/api/rest/oauth2/auth",
        "tokenUrl": "https://example.com/hub/api/rest/oauth2/token",
        "serviceId": "<<service id here>>",
        "serviceSecret": "<<service secret here>>",
        "serviceScope": "<<service scope>>",
        "baseUrl": "https://example.com/youtrack",
        "username": "<<youtrack username>>",
        "password": "<<youtrack password>>"
    }

This will export multiple files into an export folder, so don't forget to create the folder first

    mkdir export

Todo
---
* Array of column and fields should be read from a configuration file, instead of being hardcoded into convert.php
* Generalize read XML for other formats. See if we can specify the name of the element to get children from (currently, it's reading only "issues")
* Step option for API call should be a console argument

