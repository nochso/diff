<?php /* @var $this \nochso\Diff\Format\Template\HTML */ ?>
<?php if (count($this->getMessages()) > 0): ?>
<ul>
<?php foreach ($this->getMessages() as $message): ?>
    <li><?= $message ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<pre><?php foreach ($this->getDiff()->yieldDiffLines() as $key => $line): ?>
<?= $key > 0 ? "\n" : '' ?>
<?php if ($this->isShowingLineNumber()): ?><?= $this->formatLineNumber($line) ?> <?php endif; ?>
<?= $this->formatLine($line) ?>
<?php endforeach; ?></pre>