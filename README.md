Helpers
==========

A series of little PHP helper classes

Included Helpers
----------------

- JsonToList
- KeyValuePair

Basic Usage
-----------

### JsonToList

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

```
