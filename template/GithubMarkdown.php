<?php /* @var $this \nochso\Diff\Format\Template\GithubMarkdown */ ?>
<?php if (count($this->getMessages()) > 0): ?>
<?php foreach ($this->getMessages() as $message): ?>
* <?= $message ?>

<?php endforeach; ?>

<?php endif; ?>
```diff
<?php foreach ($this->getDiff()->getDiffLines() as $key => $line): ?>
<?= $this->formatLine($line) ?>

<?php endforeach; ?>
```