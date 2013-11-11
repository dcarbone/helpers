Helpers
==========

A series of little PHP helper classes

Included Helpers
----------------

- JSON To UL
- DOMPlus
- JsonWriterPlus
- XMLWriterPlus
- AbstractTraversableClass
- KeyValuePair
- JsonSerializable

Basic Usage
-----------

### JSON To UL

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

echo JSONToUl::invoke($jsonString);

```

### DOMPlus

This class was inspired by <a href="http://beerpla.net/projects/smartdomdocument-a-smarter-php-domdocument-class/" target="_blank">Artem Russakovskii's SmartDOMDocument Class</a>.

In versions of PHP prior to 5.3.6, one of the most annoying issues with the DOMDocument class is that you cannot export
to html from a specific node.

This class allows you to not only do that for older versions of PHP, but also adds a method which strips all of the non-important
elements from the output string (doctype, html, header, body).

It is an extension of the base DOMDocument class, so it has all of the same methods the base class does.  What is available depends on the version of PHP you are running.

If you have a version of PHP >= 5.3.6, it defers to base method functionality.

```php
$dom = new DOMPlus();
$dom->loadHTML('your html string');

echo $dom->saveHTMLExact();

$element = $dom->getElementById('id of element');

echo $dom->saveHTMLExact($element);
```

### AbstractTraversableClass

This class is designed to ease the annoying process of writing "Collection"-style classes in PHP.  The built-in
ArrayObject class has some inherent issues which prevent it from being a good base, so I created this abstract class to
serve in it's place.

It is designed to be extended by a concrete class of your own creation.

For more information on abstract and concrete classes, see
<a href="http://php.net/manual/en/language.oop5.abstract.php" target="_blank">http://php.net/manual/en/language.oop5.abstract.php</a>.

There is a base constructor which accepts an array of data, but nothing is lost if you do not use it.

The following interfaces are implemented:

- <a href="http://php.net/manual/en/class.arrayaccess.php" target="_blank">\ArrayAccess</a>
- <a href="http://php.net/manual/en/class.countable.php" target="_blank">\Countable</a>
- <a href="http://us2.php.net/RecursiveIterator" target="_blank">\RecursiveIterator</a>
- <a href="http://us2.php.net/manual/en/class.seekableiterator.php" target="_blank">\SeekableIterator</a>
- <a href="http://us1.php.net/manual/en/class.serializable.php" target="_blank">\Serializable</a>
- <a href="http://php.net/manual/en/class.jsonserializable.php" target="_blank">\JsonSerializable</a>

#### USERS OF PHP < 5.4

Be warned that the JsonSerializable interface was not implemented until 5.4.0.  I have included a
<a href="https://github.com/dcarbone/helpers/blob/master/lib/DCarbone/Helpers/JsonSerializable.php">definition of this interface</a>
in this Helpers library for compatibility, however you WILL NOT be able to call
```json_encode($myobj);``` while under PHP 5.3!