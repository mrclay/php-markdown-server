<?php
/** @var string $content */
/** @var object $meta */
?>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php if (!empty($meta->is_home)): ?>
  <title><?= htmlspecialchars($meta->title) ?></title>
<?php elseif (!empty($meta->title)): ?>
  <title><?= htmlspecialchars($meta->title) ?> : My Website</title>
<?php else: ?>
  <title>My Website : All about me.</title>
<?php endif; ?>
<?php if (!empty($meta->meta_description)): ?>
  <meta
    name="description"
    content="<?= htmlspecialchars($meta->meta_description) ?>"
  >
<?php else: ?>
  <meta name="description" content="All about my website.">
<?php endif; ?>
<?php
$css_mtime = filemtime(__DIR__ . '/../../public/site.css');
?>
<link rel="stylesheet" href="/site.css?<?= $css_mtime ?>" />
