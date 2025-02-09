<?php
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>
<div class="alert alert-<?= h($class) ?> alert-hover" id="flash_success">
    <button class="close">x</button>
    <?php echo $message; ?>
</div>
