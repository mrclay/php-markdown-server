# PHP Markdown Server

A minimal PHP router that serves HTML by processing Markdown files.

## Who is this for?

- You have PHP hosting
- You want to upload Markdown files to create new web pages
- You don't want a build process or log-in system
- You can build the templates with PHP
- You want a system you can understand in 5 minutes, easily extended by you or an agent

## Install

```
git clone git@github.com:mrclay/php-markdown-server.git
cd php-markdown-server
./start-local.sh

# to stop
./stop-local.sh
```

## Directory `/public`

Contents of this directory should be in the document root of the site. `_router.php` will handle
non-file requests, and handles most logic for rendering pages.

## Directory `/md-server/pages`

If a Markdown file is placed in this directory, it will be served, but without the `.md` extension.
E.g. the URL `/foo/bar` will attempt to serve the content in `pages/foo/bar.md` (processed and run
through the templates). Use `index.md` for an index URL. 

### Markdown files

At the top of each markdown file is the "frontmatter" block. This is a YAML-formatted set of metadata
that will control the page's rendering. The
[docs for `Meta`](https://github.com/mrclay/php-markdown-server/blob/main/md-server/lib.php#L10) has
full details, but a few examples:

- `title`: The page title (plain text encoding). If not the home page, the site title will be appended.
- `description`: The page's meta description (plain text encoding). Otherwise a default will be used.
- `is_home`: Set to `true` for the home page, otherwise omit this.

The markdown content is processed by [CommonMark](https://commonmark.thephpleague.com/2.x/).
After that, a few replacements are made:

## Directory `/md-server/templates`

In the PHP files you can use functions `content()` and `meta()` (both in the `MrClay\MarkdownServer`
namespace) to access the main page HTML and metadata.

## Extension ideas

Most of the logic is in `md-server/lib.php`.

As an example, I've added a snippet to replace `<p>{{OTHER_ANIMALS}}</p>` in the output with the output of the
script `files/example-replacement.php`.

