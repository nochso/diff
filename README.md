# nochso/diff

> Namespace: `\nochso\Diff`

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
```php
# First you need a Diff object
$diff = \nochso\Diff\Diff::create('foo', 'bar');

# Then you can use anything that implements the Formatter interface
$formatter = new \nochso\Diff\Format\Template\Text();

# to render the Diff object.
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

