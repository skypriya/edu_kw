<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-success alert-hover" id="flash_success">
    <button class="close">x</button>
    <?php echo $message; ?>
</div>
