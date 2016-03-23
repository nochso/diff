<?php /* @var $this \nochso\Diff\Format\Template\HTML */ ?>
<?php if (count($this->getMessages()) > 0): ?>
<ul>
<?php foreach ($this->getMessages() as $message): ?>
    <li><?= $message ?></li>
<?php endforeach; ?>
</ul>
<?php endif; ?>
<pre><?php foreach ($this->getDiff()->getDiffLines() as $key => $line): ?>
<?= $key > 0 ? "\n" : '' ?>
<?= $this->printfLine($line) ?>
<?php endforeach; ?></pre>