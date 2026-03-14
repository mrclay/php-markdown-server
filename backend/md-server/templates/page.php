<?php
/** @var string $content */
/** @var object $meta */
?>
<!doctype html>
<html lang="en">
  <head>
    <?php include __DIR__ . '/head.php'; ?>
  </head>
  <body
    class="<?= htmlspecialchars($meta->body_class ?? '') ?>"
    data-uri="<?= htmlspecialchars($meta->_request_uri) ?>"
  >
    <div class="contain">
      <header>
        <?php include __DIR__ . '/header.php' ?>
      </header>
      <div class="two-col">
        <main class="<?= htmlspecialchars($meta->main_class ?? '') ?>">
          <?= $content; ?>
        </main>
        <aside class="sidebar">
          <?php if (empty($meta->sidebar)): ?>
            <?php include __DIR__ . '/sidebar-default.php' ?>
          <?php else: ?>
            <?php include __DIR__ . "/sidebar-{$meta->sidebar}.php" ?>
          <?php endif; ?>
        </aside>
      </div>
      <footer>
        <?php include __DIR__ . '/footer.php'; ?>
      </footer>
    </div>
  </body>
</html>
