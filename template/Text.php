<?php /* @var $this \nochso\Diff\Format\Template\Text */ ?>
<?php if (count($this->getMessages()) > 0): ?>
<?php foreach ($this->getMessages() as $message): ?>
<?= $message ?>

<?php endforeach; ?>
<?php endif; ?>
<?php foreach ($this->getDiff()->yieldDiffLines() as $key => $line): ?>
<?= $key > 0 ?"\n":'' ?><?= $this->formatLineNumber($line) ?><?= $this->formatLine($line) ?>
<?php endforeach; ?>
