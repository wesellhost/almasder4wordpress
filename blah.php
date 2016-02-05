<?php

add_action('add_meta_boxes','almasder4wordpress_add_almasderbox');


/* Adds origin meta box to the post and edit screen */
function almasder4wordpress_add_originbox(){
	
	//@ref https://developer.wordpress.org/reference/functions/add_meta_box/
	$callback_func_args = array('foo' => "aka", 'bar' => $var2);
	add_meta_box( 'almasder4wordpress-originr-meta-box', __("Post/News/Article Origin",'almasder4wordpress') 'almasder4wordpress_origin_inner_custom_box', 'post', 'normal', 'high', $callback_func_args);
}

/* Prints origin meta box content */
function almasder4wordpress_origin_inner_custom_box($post, $metabox){
	?>
   <label for="myplugin_field"> Description for this field </label>
    <select name="myplugin_field" id="myplugin_field" class="postbox">
        <option value="">Select something…</option>
        <option value="something">Something</option>
        <option value="else">Else</option>
    </select>
    <?php
}

__("Yes");
__("No");

