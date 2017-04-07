<?php
echo '<dl class="ap-descr">';
echo '<dt>' . tt('Apartment ID') . ':</dt><dd>' . $data->id . '</dd>';
$rows = HFormEditor::getGeneralFields();
HFormEditor::renderViewRows($rows, $data);
echo '<dt>' . tt('Views count') . ':</dt><dd>' . $data->count_views . '</dd>';
echo '</dl>';
echo '<div class="clear"></div>';
