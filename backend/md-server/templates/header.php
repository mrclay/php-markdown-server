<?php
/** @var string $content */
/** @var object $meta */
?>
<?php if (!empty($meta->is_home)): ?>
  <h1>Example Web Site</h1>
<?php else: ?>
  <h1><a href="/">Example Web Site</a></h1>
<?php endif; ?>
<nav>
  <a href="/">Home</a> | <a href="/about">About</a> | <a href="/foo/bar">Foo/Bar</a>
</nav>