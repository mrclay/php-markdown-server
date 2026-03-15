<?php
namespace MrClay\MarkdownServer;
$css_mtime = filemtime(meta()->_public_root_path . '/site.css');
?>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<?php if (meta()->is_home): ?>
  <title><?= htmlspecialchars(meta()->title) ?></title>
<?php elseif (meta()->title): ?>
  <title><?= htmlspecialchars(meta()->title) ?> : My Website</title>
<?php else: ?>
  <title>My Website : All about me.</title>
<?php endif; ?>
<?php if (meta()->description): ?>
  <meta
    name="description"
    content="<?= htmlspecialchars(meta()->description) ?>"
  >
<?php else: ?>
  <meta name="description" content="All about my website.">
<?php endif; ?>
<link rel="stylesheet" href="/site.css?<?= $css_mtime ?>" />
