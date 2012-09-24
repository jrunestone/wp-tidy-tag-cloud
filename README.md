tidy-tag-cloud
=================

Tidy tag cloud is a nicer lightweight tag cloud that gets rid of the default inline font-size style and provides better configurability such as custom css classes, return objects instead of strings and more. It's simple to use and can be used in the same manner as the default wp_tag_cloud function.

Installation
------------

Place tidy-tag-cloud.php in your plugins folder and activate it.
This plugin removes the inline font-size style from the tag links and replaces them with size-x css classes. To make the tags appear with proper size, add the required css classes.

Usage
-----

Instead of wp_tag_cloud(), use tidy_tag_cloud(). It accepts the same parameters as wp_tag_cloud plus:

``` php
array(
	'tag_class' => '',	// css class for each individual tag, use '' for no class
	'list_class' => 'wp-tag-cloud',	// css class for the ul list, use '' for no class
	'show_default_tag_class' => false,	// show or hide the default tag class (tag-link-x)
	'show_title' => true	// show or hide link title
	'show_rel' => true	// show or hide rel="tag" tag
)
```

If the format parameter is set to array, an array of tag objects will be returned:

``` php
$tag = (object)array(
	'id' => 0,
	'link' => '',
	'name' => '',
	'size' => 0,
	'title' => '',
	'count' => 0,
	'css_class' => ''
)
```

### Filters

Remember to add the last parameters 10, 2.

``` php
// called for each individual tag
add_filter('tidy_tag_cloud_tag', function($tag, $args) {
	// modify $tag properties, no need to return anything
	$tag->title = 'Custom title';
}, 10, 2);

// called for the complete output
add_filter('tidy_tag_cloud_output', function($output, $args) {
	// modify and return $output
	$output = 'Replace the whole output';
	return $output;
}, 10, 2);
```

### Example

``` php
tidy_tag_cloud(array(
	'smallest' => 9,
	'largest' => 22,
	'unit' => 'px',
	'format' => 'flat',
	'tag_class' => 'tag',
	'show_default_tag_class' => false,
	'show_title' => false,
	'show_rel' => false
));

// output:
<a href="http://your-blog.com/tags/tag-name" class="tag size-10">Tag name</a>
...

$tags = tidy_tag_cloud(array(
	'smallest' => 9,
	'largest' => 22,
	'unit' => 'px',
	'format' => 'array',
	'tag_class' => 'tag',
	'show_default_tag_class' => false,
	'show_title' => false
));

foreach ($tags as $tag)
	echo 'Tag: ' . $tag->name;

// output:
Tag: Tag name
```

Changelog
---------

### 1.0.1

* Added the show_rel argument

### 1.0.0

* Initial release

License (GPLv3)
-------
This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.