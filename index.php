<?php
require __DIR__ . '/vendor/autoload.php';

use ComponentPHP\Engine;


$engine = new Engine();
$content = '<MyComponent message="hello world" shout="Im alive"> Some content
</MyComponent>
<MyComponent message="hello world2" shout="Im alive2"> Some content2</MyComponent>
<MyComponent message="hello worldhasnest" shout="Im alive has nest"> Some content has nest
<MyComponent message="YOLO">you only live once is stupid</MyComponent>
</MyComponent>';
echo $engine->render($content);