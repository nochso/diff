# nochso/diff

> Namespace: `\nochso\Diff` [![Travis CI build status](https://api.travis-ci.org/nochso/diff.svg)](https://travis-ci.org/nochso/diff) [![Coverage status](https://coveralls.io/repos/github/nochso/diff/badge.svg)](https://coveralls.io/github/nochso/diff) [![License: BSD 3-clause](https://img.shields.io/badge/license-BSD%203-blue.svg)](LICENSE) [![Latest tag on Github](https://img.shields.io/github/tag/nochso/diff.svg)](https://github.com/nochso/diff/tags)

Diff implementation for PHP with support for text, HTML and console output out of the box.

This library is a fork of [sebastian/diff](https://github.com/sebastianbergmann/diff): While the original diff
implementation has not notably changed, new features were added:

- Configurable limit for lines of context around the modified lines
- Plain PHP templates for displaying diffs in:
  - Plain text
  - Colored POSIX console output
  - HTML
  - Github flavoured Markdown
- Modify existing templates or create your own
- Line numbering based on the "before" string
- `Upstream` formatter for maintaining compatibility with `sebastian/diff` &mdash; and to keep the original tests around

* * * *

- [nochso/diff](#nochsodiff)
- [Installation](#installation)
- [Usage](#usage)

# Installation

```
composer require nochso/diff
```

# Usage

Start with creating a `Diff` object by passing two strings to `Diff::create()`:

```php
$diff = \nochso\Diff\Diff::create('foo', 'bar');
```

The Diff object contains a list of `DiffLine` objects, consisting of text, a
line number and the type of diff operation.

```php
foreach ($diff->getDiffLines() as $line) {
    if ($line->isRemoval()) {
        echo 'Line ' . $line->getLineNumberFrom() . " was removed:\n";
        echo $line->getText() . "\n";
    }
}
```

Most of the time you'll want to display the diff somewhere. You can pass a Diff
instance to anything that implements the `Formatter` interface:

```php
$formatter = new \nochso\Diff\Format\Template\Text();
echo $formatter->format($diff);
```

Output:
```
1: -foo
 : +bar
```

How about two lines of context and Github flavoured Markdown?
```php
$context = new ContextDiff();
$context->setMaxContext(2);
$diff = Diff::create($from, $to, $context);
$gfm = new \nochso\Diff\Format\Template\GithubMarkdown();
echo $gfm->format($diff);
```

    ```diff
     2: pariatur ground round
     3: dolore meatloaf nisi
    -4: shoulder.
     5: Consequat rump spare
    -6: ribs ham hock shank.
     7: Magna esse nisi
     8: frankfurter picanha
    ```

As you can see, when creating a Diff you can pass a ContextDiff object to change the default behaviour.

