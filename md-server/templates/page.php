<?php
namespace MrClay\MarkdownServer;
$sidebar = meta()->sidebar ?: 'default';
?>
<!doctype html>
<html lang="en">
  <head>
    <?php require __DIR__ . '/head-elements.php'; ?>
  </head>
  <body
    class="<?= htmlspecialchars(meta()->body_class ?? '') ?>"
    data-pathname="<?= htmlspecialchars(meta()->_request_pathname) ?>"
  >
    <div class="contain">
      <header>
        <?php require __DIR__ . '/header.php' ?>
      </header>
      <div class="two-col">
        <main class="<?= htmlspecialchars(meta()->main_class ?? '') ?>">
          <?= content(); ?>
        </main>
        <aside class="sidebar">
          <?php require __DIR__ . "/sidebar-{$sidebar}.php" ?>
        </aside>
      </div>
      <footer>
        <?php require __DIR__ . '/footer.php'; ?>
      </footer>
    </div>
  </body>
</html>
