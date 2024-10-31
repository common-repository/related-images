<style>
table.related-images-table {
	width: 100%;
	margin: 10px 0 0 0;
}

table.related-images-table td, table.related-images-table th {
	text-align: left;
    border-bottom: 1px solid #D4D4D4;
    padding: 7px 5px;
    vertical-align: middle;
}
table.related-images-table td img {
    vertical-align: middle;
}

table.related-images-table tr:nth-child(2n) {
    background-color: #FFF;
}
table.related-images-table tr:nth-child(2n+1) {
    background-color: #F1F1F1;
}
table.related-images-table {
    border-collapse: collapse;
}
.add-positions-link {
	white-space: nowrap;
	display: inline-block;
	padding: 8px 4px 4px 4px;
}
.new-position-wrapper {
	padding: 4px;
}
.new-position-wrapper label {
	display: block;
	padding: 2px 0 4px;
}
.new-position-wrapper .button {
	margin-left: 5px;
}
.new-position-wrapper input[type="text"] {
	margin-bottom: 10px;
}
.postbox .new-position-wrapper .spinner {
	float: none;
	margin: 0 5px 0;
}




</style>

<a href="#" class="ri-open-media button">
	<span class="wp-media-buttons-icon"></span>
	Open the Media Manager
</a>

<table class="related-images-table" id="related-list">
<tr>
	<th>Image</th>
	<th>Position</th>
	<th>Remove</th>
</tr>
<?php
$i = 0;
if ( $related_images ) {
	foreach ($related_images as $key => $value) {

		$image_attributes = wp_get_attachment_image_src( $value['img_id'] );

		echo '<tr id="tr_'.$i.'_'.$value['img_id'].'_'.$value['position'].'">';
			echo '<td><img src="'.$image_attributes[0].'" width="30" height="auto"></td>';
			echo '<td>';

			$defined_positions = \RI\Backend::get()->get_all_positions();

			echo '<select name="related-images-select[]">';
			foreach ($defined_positions as $pos_key => $pos ) {
				echo '<option value="'.$value['img_id'].'_'.$pos_key.'" '.selected( $value['position'], $pos_key ).'>'.$pos.'</option>';
			}
			echo '</select>';

			echo '</td>';
			echo '<td><a href="#" id="'.$i.'_'.$value['img_id'].'_'.$value['position'].'" class="remove-related-image">remove</a></td>';
		echo '</tr>';
		$i++;

	}
}
?>
</table>
<span class="add-positions-link"><a id="show-add-position" href="">Edit positions</a></span>