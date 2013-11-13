Helpers
==========

A series of little PHP helper classes

Included Helpers
----------------

- JsonToUl
- DOMPlus
- JsonWriterPlus
- XMLWriterPlus
- AbstractTraversableClass
- KeyValuePair
- JsonSerializable

Basic Usage
-----------

### AbstractTraversableClass

The primary purpose of this class is to bring OO-style functionality to a world which is predominantly developed using purely scalar types in a procedural fashion.

There is nothing wrong with doing procedural-based code if you are comfortable with it, however as times change so must PHP.  There is a build-in class which attempts to do this same thing,
which you can see here: <a href="http://php.net/manual/en/class.arrayobject.php" target="_blank">ArrayObject</a>, however
in my testing with this class even low to mid complexity maps such as ArrayObject<Int|String, ArrayObject<Int|String, ArrayObject<Int|String,String>>> notation
(parent with children who also have children, etc) wherein I had custom classes extending the base ArrayObject class, I ran into some
fundamental issues with ArrayObject and was ultimately unable to use it for what I desired.

To this end, I have written my own class which replicates _to a point_ the functionality of the built-in class, while adding
some additional features from the absolutely excellent <a href="http://www.doctrine-project.org/" target="_blank">Doctrine Project</a>
<a href="http://www.doctrine-project.org/api/common/2.4/source-class-Doctrine.Common.Collections.ArrayCollection.html" target="_blank">ArrayCollection</a> class.
Lots of thanks goes to the <a href="http://www.doctrine-project.org/about.html" target="_blank">Doctrine Team</a> for the inspiration
for this class.

#### Example

```php

use DCarbone\Helpers\AbstractTraversableClass;

class Parent extends AbstractTraversableClass {}
class Child extends AbstractTraversableClass {}

$sasha = new Child(array(
    'age' => 7,
    'name' => 'Sasha',
    'favoriteFood' => 'French Fries'
));

$peter = new Child(array(
    'age' => 15,
    'name' => 'Peter',
    'favoriteFood' => 'Salmon'
));

$parent = new Parent();

$parent->set('Peter', $peter);
$parent->set('Sasha', $sasha);

var_dump($parent->array_keys());
/*
array(2) {
    [0] => string(5) "Peter"
    [1] => string(5) "Sasha"
}
*/

$parent->rsort();

var_dump($parent->array_keys());
/*
array(2) {
    [0] => string(5) "Sasha"
    [1] => string(5) "Peter"
}
*/

$daniel = array(
    "age" => 27,
    "name" => "Daniel",
    "favoriteFood" => "Spaghetti"
);

$parent->append($daniel);

var_dump($parent->array_keys());
/*
array(3) {
    [0] => string(5) "Sasha"
    [1] => string(5) "Peter"
    [2] => int(0)
}
*/
```

#### Available Methods

```php
// Creates instance and defines default data
public __construct (array $data = array())

// Returns keys present on this collection
public array_keys()

// Set a value in the collection using a scalar key and any value
public set (scalar $key, mixed $value)

// Append any value to this collection.  Will receive an integer key
public append (mixed $value)

// Uses strict comparison.
// For more information on comparing objects, see here:
// http://php.net/manual/en/language.oop5.object-comparison.php
public contains (mixed $element)

// Accepts a closure function to perform a custom contains() call
// Closure params: $closure($key, $value)
public exists (\Closure $func)

// Returns index of value or false on failure / non-existence
public indexOf (mixed $value)

// Remove element from collection based on key.  Returns removed element or null.
public remove (scalar $key)

// Remove element from collection based on element.  Returns true on success, false on failure / non-existence
public removeElement (mixed $element)

// Returns new \ArrayIterator instance with data from collection
public getIterator ()

// Return new instance of extended class after applying array_map to internal contents
public map (\Closure $func)

// Apply closure to internal data set.  Returns bool
public filter (\Closure $func)

// Is this collection empty?
public isEmpty ()

// Returns first item in collection or null if empty
public first ()

// Returns last item in collection or null if empty
public last ()

// Sort internal data by value.  Accepts PHP SORT_X flags
public sort (int $flags)

// Reverse sort internal data by value.
public rsort (int $flags)

// Sort by values with custom sort function.
public usort (mixed $func)

// Sort by keys
public ksort (int $flags)

// Reverse sort by keys
public krsort (int $flags)

// Sort by keys with custom sort function
public uksort (mixed $func)

// Sort by values preserving indices
public asort (int $flags)

// Reverse sort by values preserving indices
public arsort (int $flags)

// Sort by values preserving indices with custom sort function
public uasort (mixed $func)

```

You may also use this class as a glorified array if you are used to that kind of thing.

For instance, this works:

```php
foreach($parent as $name=>$child)
{
    echo $name.' : ';
    echo $child['age'];
    echo '<br>';
}
/*
    Sasha : 7
    Peter : 15
    0 : 27
*/
```

However you may NOT use foreach by reference:

```php
foreach($parent as $name=>&$child) {} // Doesn't work!
```

If you wish to modify the values of the contents within a foreach loop, you can do this:

```php
foreach($parent->array_keys() as $key)
{
    $child = &$parent[$key];
}
```

If you are using PHP >= 5.4.0, you may also call this:

```php
$json = json_encode($parent);
```

Note that this will only work if all child objects also implement the <a href="http://php.net/manual/en/class.jsonserializable.php">JsonSerializable</a> interface.

#### PHP 5.3.x USERS!

I have added a custom JsonSerializable interface to maintain backward compatibility, but you must call this:

```php
$json = json_encode($parent->jsonSerialize());
```

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

echo JsonToUl::invoke($jsonString);

```

### DOMPlus

This class was inspired by <a href="http://beerpla.net/projects/smartdomdocument-a-smarter-php-domdocument-class/" target="_blank">Artem Russakovskii's SmartDOMDocument Class</a>.

In versions of PHP prior to 5.3.6, one of the most annoying issues with the DOMDocument class is that you cannot export
to html from a specific node.

This class allows you to not only do that for older versions of PHP, but also adds a method which strips all of the non-important
elements from the output string (doctype, html, header, body).

It is an extension of the base DOMDocument class, so it has all of the same methods the base class does.  What is available depends on the version of PHP you are running.

If you have a version of PHP >= 5.3.6, it uses base method functionality.

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