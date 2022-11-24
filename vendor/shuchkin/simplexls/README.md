# SimpleXLS class (Official)
[<img src="https://img.shields.io/packagist/dt/shuchkin/simplexls" />](https://packagist.org/packages/shuchkin/simplexls)
[<img src="https://img.shields.io/github/license/shuchkin/simplexls" />](https://github.com/shuchkin/simplexls/blob/master/license.md) [<img src="https://img.shields.io/github/stars/shuchkin/simplexls" />](https://github.com/shuchkin/simplexls/stargazers) [<img src="https://img.shields.io/github/forks/shuchkin/simplexls" />](https://github.com/shuchkin/simplexls/network) [<img src="https://img.shields.io/github/issues/shuchkin/simplexls" />](https://github.com/shuchkin/simplexls/issues)
[<img src="https://img.shields.io/opencollective/all/simplexls" />](https://opencollective.com/simplexls)
[<img src="https://img.shields.io/badge/patreon-_-_" />](https://www.patreon.com/shuchkin)

Parse and retrieve data from Excel xls files. MS Excel 2007 workbooks PHP reader.
No addiditional extensions need (internal unzip + standart SimpleXML parser).

See also:<br/>
[SimpleXLS](https://github.com/shuchkin/simplexls) old format MS Excel 97 php reader.<br/>
[SimplexlsGen](https://github.com/shuchkin/simplexlsgen) xls php writer.  

*Hey, bro, please ★ the package for my motivation :) and [donate](https://opencollective.com/simplexls) for more motivation!*

**Sergey Shuchkin** <sergey.shuchkin@gmail.com>

## Basic Usage
```php
if ( $xls = Simplexls::parse('book.xls') ) {
	print_r( $xls->rows() );
} else {
	echo Simplexls::parseError();
}
```
```
Array
(
    [0] => Array
        (
            [0] => ISBN
            [1] => title
            [2] => author
            [3] => publisher
            [4] => ctry
        )

    [1] => Array
        (
            [0] => 618260307
            [1] => The Hobbit
            [2] => J. R. R. Tolkien
            [3] => Houghton Mifflin
            [4] => USA
        )

)
```
```
// Simplexls::parse( $filename, $is_data = false, $debug = false ): Simplexls (or false)
// Simplexls::parseFile( $filename, $debug = false ): Simplexls (or false)
// Simplexls::parseData( $data, $debug = false ): Simplexls (or false)
```

## Installation
The recommended way to install this library is [through Composer](https://getcomposer.org).
[New to Composer?](https://getcomposer.org/doc/00-intro.md)

This will install the latest supported version:
```bash
$ composer require shuchkin/simplexls
```
or download class [here](https://github.com/shuchkin/simplexls/blob/master/src/Simplexls.php)

## Examples
### xls to html table
```php
echo Simplexls::parse('book.xls')->toHTML();
```
or
```php
if ( $xls = Simplexls::parse('book.xls') ) {
	echo '<table border="1" cellpadding="3" style="border-collapse: collapse">';
	foreach( $xls->rows() as $r ) {
		echo '<tr><td>'.implode('</td><td>', $r ).'</td></tr>';
	}
	echo '</table>';
} else {
	echo Simplexls::parseError();
}
```
### xls read cells, out commas and bold headers
```php
echo '<pre>';
if ( $xls = Simplexls::parse( 'xls/books.xls' ) ) {
	foreach ( $xls->rows() as $r => $row ) {
		foreach ( $row as $c => $cell ) {
			echo ($c > 0) ? ', ' : '';
			echo ( $r === 0 ) ? '<b>'.$cell.'</b>' : $cell;
		}
		echo '<br/>';
	}
} else {
	echo Simplexls::parseError();
}
echo '</pre>';
```
### xls get sheet names and sheet indexes
```php
if ( $xls = Simplexls::parse( 'xls/books.xls' ) ) {
	print_r( $xls->sheetNames() );
	print_r( $xls->sheetName( $xls->activeSheet ) );
}
// Sheet numeration started 0
```
```
Array
(
    [0] => Sheet1
    [1] => Sheet2
    [2] => Sheet3
)
Sheet2
```
### Gets extend cell info by ->rowsEx()
```php
print_r( Simplexls::parse('book.xls')->rowsEx() );
```
```
Array
(
    [0] => Array
        (
            [0] => Array
                (
                    [type] => s
                    [name] => A1
                    [value] => ISBN
                    [href] => 
                    [f] => 
                    [format] => 
                    [r] => 1
                    [hidden] =>
                )

            [1] => Array
                (
                    [type] => 
                    [name] => B1
                    [value] => 2016-04-12 13:41:00
                    [href] => 
                    [f] => 
                    [format] => m/d/yy h:mm
                    [r] => 2
                    [hidden] => 1
                )
```
### Select Sheet
```php
$xls = Simplexls::parse('book.xls');
print_r( $xls->rows(1) ); // Sheet numeration started 0, we select second worksheet
```
### Get sheet by index 
```php
$xls = Simplexls::parse('book.xls');	
echo 'Sheet Name 2 = '.$xls->sheetName(1);
```
### xls::parse remote data
```php
if ( $xls = Simplexls::parse('http://www.example.com/example.xls' ) ) {
	$dim = $xls->dimension(1); // don't trust dimension extracted from xml
	$num_cols = $dim[0];
	$num_rows = $dim[1];
	echo $xls->sheetName(1).':'.$num_cols.'x'.$num_rows;
} else {
	echo Simplexls::parseError();
}
```
### xls::parse memory data
```php
// For instance $data is a data from database or cache    
if ( $xls = Simplexls::parseData( $data ) ) {
	print_r( $xls->rows() );
} else {
	echo Simplexls::parseError();
}
```
### Get Cell (slow)
```php
echo $xls->getCell(0, 'B2'); // The Hobbit
``` 
### DateTime helpers
```php
// default Simplexls datetime format YYYY-MM-DD HH:MM:SS (MySQL)
echo $xls->getCell(0,'C2'); // 2016-04-12 13:41:00

// custom datetime format
$xls->setDateTimeFormat('d.m.Y H:i');
echo $xls->getCell(0,'C2'); // 12.04.2016 13:41

// unixstamp
$xls->setDateTimeFormat('U');
$ts = $xls->getCell(0,'C2'); // 1460468460
echo gmdate('Y-m-d', $ts); // 2016-04-12
echo gmdate('H:i:s', $ts); // 13:41:00

// raw excel value
$xls->setDateTimeFormat( NULL ); // returns as excel datetime
$xd = $xls->getCell(0,'C2'); // 42472.570138889
echo gmdate('m/d/Y', $xls->unixstamp( $xd )); // 04/12/2016
echo gmdate('H:i:s', $xls->unixstamp( $xd )); // 13:41:00 
```
### Rows with header values as keys
```php
if ( $xls = Simplexls::parse('books.xls')) {
	// Produce array keys from the array values of 1st array element
	$header_values = $rows = [];
	foreach ( $xls->rows() as $k => $r ) {
		if ( $k === 0 ) {
			$header_values = $r;
			continue;
		}
		$rows[] = array_combine( $header_values, $r );
	}
	print_r( $rows );
}
```
```
Array
(
    [0] => Array
        (
            [ISBN] => 618260307
            [title] => The Hobbit
            [author] => J. R. R. Tolkien
            [publisher] => Houghton Mifflin
            [ctry] => USA
        )
    [1] => Array
        (
            [ISBN] => 908606664
            [title] => Slinky Malinki
            [author] => Lynley Dodd
            [publisher] => Mallinson Rendel
            [ctry] => NZ
        )
)
```
### Debug
```php
ini_set('error_reporting', E_ALL );
ini_set('display_errors', 1 );

if ( $xls = Simplexls::parseFile('books.xls', true ) ) {
	echo $xls->toHTML();
} else {
	echo Simplexls::parseError();
}
```
### Classic OOP style 
```php
$xls = new Simplexls('books.xls'); // try...catch
if ( $xls->success() ) {
	print_r( $xls->rows() );
} else {
	echo 'xls error: '.$xls->error();
}
```
More examples [here](https://github.com/shuchkin/simplexls/tree/master/examples)

### Error Codes
Simplexls::ParseErrno(), $xls->errno()<br/>
<table>
<tr><th>code</th><th>message</th><th>comment</th></tr>
<tr><td>1</td><td>File not found</td><td>Where file? UFO?</td></tr>
<tr><td>2</td><td>Unknown archive format</td><td>ZIP?</td></tr>
<tr><td>3</td><td>XML-entry parser error</td><td>bad XML</td></tr>
<tr><td>4</td><td>XML-entry not found</td><td>bad ZIP archive</td></tr>
<tr><td>5</td><td>Entry not found</td><td>File not found in ZIP archive</td></tr>
<tr><td>6</td><td>Worksheet not found</td><td>Not exists</td></tr>
</table>