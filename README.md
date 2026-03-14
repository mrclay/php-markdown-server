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
./start-local.sh
```

## Directory `/backend/public`

Contents of this directory should be in the document root of the site. `_router.php` will handle
non-file requests, and handles most logic for rendering pages.

## Directory `/backend/md-server/pages`

If a Markdown file is placed in this directory, it will be served, but without the `.md` extension.
E.g. the URL `/foo/bar` will attempt to serve the content in `pages/foo/bar.md` (processed and run
through the templates). Use `index.md` for an index URL. 

### Markdown files

At the top of each markdown file is the "frontmatter" block. This is a YAML-formatted set of metadata
that will control the page's rendering.

- `title`: The page title (plain text encoding). If not the home page, the site title will be appended.
- `meta_description`: The page's meta description (plain text encoding). Otherwise a default will be used.
- `is_home`: Set to `true` for the home page, otherwise omit this.
- `sidebar`: Set to use a different sidebar template (e.g. `about` => `sidebar-about.php`).
- `body_class`: Adds CSS classname(s) to the body element.
- `main_class`: Adds CSS classname(s) to the main element.
- `file`: If given a path (relative to `md-server/files`), the Markdown is ignored and instead the file
          is [included using PHP](https://www.php.net/manual/en/function.include.php) and the output is placed in the page.
- `increase_headings`: If set to `true`, the markdown headings like `###` will be increased by one level. 

The markdown content is processed by [CommonMark](https://commonmark.thephpleague.com/2.x/).
After that, a few replacements are made:

## Extension ideas

As an example, I've added a snippet to replace `<p>{{OTHER_ANIMALS}}</p>` with the output of the
script `files/example-replacement.php`.

