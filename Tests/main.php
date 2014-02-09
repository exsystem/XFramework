<!DOCTYPE html>
<html>
<head>
    <title></title>
</head>
<body>
<p>Main view - begin</p>
<?php $this->View('sub'); ?>
<p>Main view - end</p>

<p><?php echo $this->Data('ViewData'); ?></p>
</body>
</html>