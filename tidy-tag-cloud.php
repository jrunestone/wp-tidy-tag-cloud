<?php
/*
Plugin Name: Tidy tag cloud
Description: Displays (or returns) a nicer tag cloud without inline style and better configurability.
Version: 1.0.2
Plugin URI: https://github.com/swemaniac/wp-tidy-tag-cloud
Author: Johan Johansson
Author URI: http://bazooka.se
License: GPLv3

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
*/

/*
Use in the same way as you would wp_tag_cloud.
This plugin removes the inline font-size style from the tag links and replaces them with size-x css classes. To make the tags appear with proper size, add the required css classes.

Accepts default wp_tag_cloud arguments plus:
	tag_class: Css class for each individual tag, use '' for no class
	list_class: Css class for the ul list, use '' for no class
	show_default_tag_class: Show or hide the default tag class (tag-link-x)
	show_title: Show or hide link title
	show_rel: Show or hide rel="tag" tag
	format: If set to array, will return tag objects instead of default link strings

Filters:
	tidy_tag_cloud_tag($tag, $args): For each individual tag (modify $tag).
	tidy_tag_cloud_output($output, $args): Filter the whole output (return $output).

Tag object:
	id, link, name, size, title, count, css_class
*/
function tidy_tag_cloud($args = '') {
	$defaults = array(
		'tag_class' => '',
		'list_class' => 'wp-tag-cloud',
		'show_default_tag_class' => false,
		'show_title' => true,
		'show_rel' => true,
		'echo' => true,
		'separator' => "\n"
	);

	$args = wp_parse_args($args, $defaults);

	$wp_tags = wp_tag_cloud(array_merge($args, array(
		'format' => 'array',
		'echo' => false
	)));

	$tags = array();
	$list = isset($args['format']) && $args['format'] === 'list';
	$tag_class = (isset($args['tag_class']) && strlen($args['tag_class']) > 0 ? ($args['tag_class'] . ' ') : '');

	if ($wp_tags) {
		foreach ($wp_tags as $tag) {
			preg_match('/class=\'tag-link-(\d+)\'/i', $tag, $id);
			preg_match('/href=\'([^\']+)\'/i', $tag, $url);
			preg_match('/title=\'(\d+)/i', $tag, $count);
			preg_match('/title=\'([^\']+)\'/i', $tag, $title);
			preg_match('/font-size: (\d+)/i', $tag, $size);
			preg_match('/<a[^>]+>([^<]+)<\/a>/i', $tag, $name);

			$tags[] = (object)array(
				'id' => $id[1],
				'link' => $url[1],
				'name' => $name[1],
				'size' => $size[1],
				'title' => $title[1],
				'count' => $count[1],
				'css_class' => $tag_class . ($args['show_default_tag_class'] ? ('tag-link-' . $id[1] . ' ') : '') . 'size-' . $size[1]
			);
		}
	}

	if (empty($wp_tags))
		return $args['format'] === 'array' ? array() : '';

	if ($args['format'] === 'array')
		return $tags;

	$output = array();

	if ($list)
		$output[] = '<ul' . ($args['list_class'] ? (' class="' . $args['list_class'] . '"') : '') . '>';

	foreach ($tags as $tag) {
		apply_filters('tidy_tag_cloud_tag', $tag, $args);

		$class = isset($tag->css_class) && strlen($tag->css_class) > 0 ? (' class="' . $tag->css_class . '"') : '';
		$title = $args['show_title'] == true && isset($tag->title) && strlen($tag->title) > 0 ? (' title="' . $tag->title . '"') : '';
		$rel = $args['show_rel'] == true ? ' rel="tag"' : '';

		$link = '<a href="' . $tag->link . '"' . $class . $title . $rel . '>' . $tag->name . "</a>";

		if ($list)
			$link = "<li>$link</li>";

		$output[] = $link;
	}

	if ($list)
		$output[] = '</ul>';

	$output = join($list ? '' : $args['separator'], $output);
	$output = apply_filters('tidy_tag_cloud_output', $output, $args);

	if ($args['echo'])
		echo $output;

	return $output;
}