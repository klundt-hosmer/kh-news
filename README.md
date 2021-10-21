# News Module

## Requirements

SilverStripe 4

## Overview

Forked from Nyeholt news and upgraded for SS 4

The News module provides a straightforward method for creating and publishing
news articles on a website. In some respects it is similar to the Blog module,
however news articles are meant to be focused more around press release style
content - this means a News Article can be represented by normal Content, 
a hosted file (eg a PDF) or a completely remote article on a separate website.
Additionally, News Articles allow authors to specify a separate Summary from
the main content, useful for aggregating content references on your site, and
allows authors to attach a thumbnail for an article.

News Holders can be configured to automatically file contained articles into
a date based hierarchy, generating a hierarchy for archive purposes. 

Unlike the Blog module, the News Module does not support widgets at all, and
does not come with Comments enabled by default. While these things could be
added on by yourself, it is not core to the functionality of the module.

## Installation
This module is not in packagist, so a few steps are required.

Specify the `klundt-hosmer/kh-news` as a repository by adding this to `composer.json`:
```
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/klundt-hosmer/kh-news.git"
    }
  ],
 ```
 
 then add the module with
 `composer require klundt-hosmer/kh-news:^1.3.2`
 
## Documentation

The News module also provides functionality to automatically file 
articles beneath a hierarchy ordered by Year, Month and day. This will then
automatically provide an "archive" type functionality for news articles. To 
enable this, select the checkbox for "Automatically file contained articles" 
on the News Holder. 

## Known issues

When creating articles with the automatic filing functionality, and its 
eventual parent location isn't visible, the article will initially appear in 
the root of the site tree, even though it has been created underneath the
correct location. Refreshing the tree fixes this problem. 

Pagination will not work when using the SubSections loop when the holder
contains child holder pages because the Pagination details will be separate for
each child holder page.
Use the TotalChildArticles loop instead of SubSections to get around this.

```
<% if TotalChildArticles %>
		<div <% if FirstLast %>class="$FirstLast"<% end_if %>>
			<% include NewsListItem %>
		</div>
		<% if TotalChildArticles.MoreThanOnePage %>
			<div id="NextPrevLinks" class="news-pagination">
			  <% if TotalChildArticles.NotLastPage %>
				<div id="NextLink" class="next">
					<p><a class="next" href="$TotalChildArticles.NextLink" 
						title="View the next page">See older articles</a></p>
				</div>
			  <% end_if %>
			  <% if TotalChildArticles.NotFirstPage %>
				<div id="PrevLink" class="previous">
					<p><a class="prev" href="$TotalChildArticles.PrevLink" 
						title="View the previous page">See newer articles</a></p>
				</div>
			  <% end_if %>
			  <span>
				<% if TotalChildArticles.PaginationSummary %><% loop TotalChildArticles.PaginationSummary %>
				  <% if CurrentBool %>
					<p class="current">
						$PageNum
					</p>
				  <% else %>
					<a class="pagination-link" href="$Link" i
						title="View page number $PageNum">$PageNum</a>
				  <% end_if %>
				<% end_loop %><% end_if %>
			  </span>
			</div>
		 <% end_if %>
<% end_if %>
```
