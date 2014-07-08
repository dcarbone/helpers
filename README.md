Helpers
==========

A series of little PHP helper classes

Included Helpers
----------------

- JsonToList
- KeyValuePair
- FileHelper

## JsonToList

```php
$jsonString = <<<STRING
{
   "glossary": {
       "title": "example glossary",
    "GlossDiv": {
           "title": "S",
        "GlossList": {
               "GlossEntry": {
                   "ID": "SGML",
                "SortAs": "SGML",
                "GlossTerm": "Standard Generalized Markup Language",
                "Acronym": "SGML",
                "Abbrev": "ISO 8879:1986",
                "GlossDef": {
                       "para": "A meta-markup language, used to create markup languages such as DocBook.",
                    "GlossSeeAlso": ["GML", "XML"]
                   },
                "GlossSee": "markup"
               }
           }
       }
   }
}
STRING;

echo JsonToList::invoke($jsonString);

// Alternatively you can have a \DOMDocument object returned to you
$dom = JsonToList::invoke($jsonString, true);

## FileHelper

For now, this class is very simple.  The only method available at the moment is ``` getLineCount ```, which is used as such:

```php
$line_count = \DCarbone\Helpers\FileHelper::getLineCount("full_path_to_file");
```

You may optionally pass in the string "linux" or "windows" to explicitly define which command is used, however if 2nd
parameter is passed, the method will attempt to determine what type of system it is on by looking at the value of
``` DIRECTORY_SEPARATOR ```

```