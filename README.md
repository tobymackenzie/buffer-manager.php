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

Here is an example for an HTML document, illustrating how it might be used to output the buffers in an HTML document, or in a JSON representation of that document if the request is via AJAX.

```PHP
<?php
$bufferManager->start('main');
include($mainContentFile);
$bufferManager->end();
$bufferManager->start('aside');
include($asideContentFile);
$bufferManager->end();

if($isAjaxRequest){
	echo json_encode(Array(
		'title'=> $pagetitle
		,'main'=> $bufferManager->get('main')
		,'aside'=> $bufferManager->get('aside')
	));
}else{
?>
<!DOCTYPE html>
<html>
	<title><?=$pagetitle?></title>
	…
	<main><?=$bufferManager->get('main')?></main>
	<aside><?=$bufferManager->get('aside')?></aside>
	…
</html>
```
