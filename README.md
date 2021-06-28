Dompdf
======

**Dompdf is an HTML to PDF converter**

At its heart, dompdf is (mostly) a [CSS 2.1](http://www.w3.org/TR/CSS2/) compliant
HTML layout and rendering engine written in PHP. It is a style-driven renderer:
it will download and read external stylesheets, inline style tags, and the style
attributes of individual HTML elements. It also supports most presentational
HTML attributes.

## Requirements

* PHP 7.1+
* Moodle 3.5+
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

#### Images stored in Moodle HTML editor.

In case you want to convert to PDF HTML that does contain images coming from Moodle internal file systems you 
need to employ the image recoding for each specific field.

For example if you have a place where you can set the image in HTML editor when you retrieve it from database 
to display it on screen you use this code:

    $rawtext = $DB->get_field('sometable', 'somefield', ['id' => 123]);
    $options = [
        'noclean' => true, 'para' => false, 'filter' => true,
        'context' => $context, 'overflowdiv' => true
    ];
    $intro = file_rewrite_pluginfile_urls(
        $rawtext, 'pluginfile.php', $context->id, $component, $filearea, $itemid
    );
    $value = format_text($intro, $format, $options, null);

For Dmpdf this does not work. There is a specific rewrite method that encode's images directly into html
and makes them usable by library:

    $rawtext = $DB->get_field('sometable', 'somefield', ['id' => 123]);
    $options = [
        'noclean' => true, 'para' => false, 'filter' => true,
        'context' => $context, 'overflowdiv' => true
    ];
    $intro = \local_dompdf\api\pdf::file_rewrite_image_urls(
        $rawtext, $itemid, $filearea, $contextid, $component 
    );
    $value = format_text($intro, $format, $options, null);

### Examples

Examples are located in examples directory of the plugin.
