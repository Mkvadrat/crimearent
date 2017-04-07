<?php
echo '<dl class="ap-descr">';
echo '<dt>' . tt('Apartment ID') . ':</dt><dd>' . $data->id . '</dd>';
$rows = HFormEditor::getGeneralFields();
HFormEditor::renderViewRows($rows, $data);
echo '</dl>';
echo '<div class="clear"></div>';
