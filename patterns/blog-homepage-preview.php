<?php
/**
 * Title: Blog — Homepage Preview
 * Slug: blue-sage/blog-homepage-preview
 * Categories: blue-sage-blog
 * Description: Featured latest post above a three-card standard grid. Classic homepage blog section.
 * Keywords: blog, posts, articles, news, homepage
 *
 * @package BlueSage
 * @author Ilyas Serter <hello@sagegrids.com>
 * @company SAGE GRIDS LTD <https://www.sagegrids.com>
 * @link https://www.iserter.com
 */
?>
<!-- wp:group {"layout":{"type":"constrained"}} -->
<div class="wp-block-group">

<!-- wp:blue-sage/blog-cards {
	"layout":"featured",
	"eyebrow":"From the blog",
	"heading":"Latest thinking",
	"postsPerPage":1,
	"showExcerpt":true,
	"showReadTime":true,
	"showAuthor":true,
	"ctaLabel":"",
	"ctaUrl":""
} /-->

<!-- wp:blue-sage/blog-cards {
	"layout":"standard",
	"eyebrow":"",
	"heading":"",
	"postsPerPage":3,
	"showExcerpt":true,
	"showReadTime":true,
	"showAuthor":true,
	"ctaLabel":"View all posts",
	"ctaUrl":"/blog/"
} /-->

</div>
<!-- /wp:group -->
