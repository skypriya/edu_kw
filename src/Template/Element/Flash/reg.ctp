<?php
if (!isset($params['escape']) || $params['escape'] !== false) {
    $message = h($message);
}
?>


<div class="alert alert-error">
      <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <?php echo $message; ?>
    </div>
