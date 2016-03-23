<?php /* @var $this \nochso\Diff\Format\Template\Text */ ?>
<?php if (count($this->getMessages()) > 0): ?>
<?php foreach ($this->getMessages() as $message): ?>
<?= $message ?>

<?php endforeach; ?>
<?php endif; ?>
<?php foreach ($this->getDiff()->getDiffLines() as $key => $line): ?>
<?= $key > 0 ?"\n":'' ?><?= $this->printfLine($line) ?>
<?php endforeach; ?>
