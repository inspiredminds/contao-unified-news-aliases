[![](https://img.shields.io/packagist/v/inspiredminds/contao-unified-news-aliases.svg)](https://packagist.org/packages/inspiredminds/contao-unified-news-aliases)
[![](https://img.shields.io/packagist/dt/inspiredminds/contao-unified-news-aliases.svg)](https://packagist.org/packages/inspiredminds/contao-unified-news-aliases)

Contao Unified News Aliases
===========================

This Contao extensions allows you to use the same news alias for the same news article translated into different languages.

In Contao, each and every news article must have a unique alias. However there are use cases where you might want to use the same alias for translated news articles. This extension allows you to always use the alias of the main news archive in the front end.

_Note:_ this extension does not actually allow you to _save_ duplicate aliases in the back end. The extension only affects the news URLs ofr news modules in the front end and provides an additional newsreader module.

## Usage

Assuming that the language settings of your news archives are already properly set up there are only two steps necessary to use the functionality of this extension:

1. Enable the unified aliases in the language settings of your main news archive.
2. Use the _Newsreader for unified aliases_ newsreader module instead of the regular newsreader module.
