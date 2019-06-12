Dompdf
======

**Dompdf is an HTML to PDF converter**

At its heart, dompdf is (mostly) a [CSS 2.1](http://www.w3.org/TR/CSS2/) compliant
HTML layout and rendering engine written in PHP. It is a style-driven renderer:
it will download and read external stylesheets, inline style tags, and the style
attributes of individual HTML elements. It also supports most presentational
HTML attributes.

## Requirements

 * Moodle 2.9+
 * MBString extension

### Recommendations

 * OPcache (OPcache, XCache, APC, etc.): improves performance
 * IMagick or GMagick extension: improves image processing performance

Visit the wiki for more information:
https://github.com/dompdf/dompdf/wiki/Requirements

### Internal settings

By default whenever a PDF is generated systems uses `$CFG->localcachedir/dompdf` to store temporary data.

### Usage

To create new instance of the PDF class use following code:

    $pdf = \local_dompdf\api\pdf::createnew();

This gives you an instance of `\Dompdf\Dompdf` class and you just use it as outlined in the 
official [library documentation](https://github.com/dompdf/dompdf/wiki).

### Examples

Examples are located in examples directory of the plugin.


