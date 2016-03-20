<?php /* @var $this \nochso\Diff\Format\Template\GithubMarkdown */ ?>
<?php if (count($this->getMessages()) > 0): ?>
<?php foreach ($this->getMessages() as $message): ?>
* <?= $message ?>

<?php endforeach; ?>

<?php endif; ?>
<?php $lines = $this->getDiff(); ?>
<?php $lineCount = count($lines); ?>
```diff
<?php foreach ($lines as $key => $line): ?>
<?= $this->formatLine($line) ?>

<?php endforeach; ?>
```