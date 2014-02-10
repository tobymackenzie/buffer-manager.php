PHP Buffer Manager
==================
A simple manager for PHP output buffers.  Can manage named or unnamed buffers.

Useage
------
The most common useage for this class is named output buffers, so that you can buffer multiple pieces of a page and then output them later in specific places.

```PHP
<?php
$bufferManager = new TJM\Component\BufferManager\BufferManager();

$bufferManager->start('block1');
echo 'This is in block 1';
$bufferManager->end();

$bufferManager->start('block2');
echo 'This is in block 2';
?>
<div class="wrapper">
	<h2>This is block 1's heading</h2>
	<div class="block1"><?php echo $bufferManager->get('block1'); ?></div>
	<h2>This is block 2's heading</h2>
	<div class="block2"><?php echo $bufferManager->get('block2'); ?></div>
</div>
```
