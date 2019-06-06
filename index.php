<?php
global $maybe;

function maybe_active_menu($what) {
    global $maybe;
    if ($what == $maybe) {
        echo 'class="active"';
    }
}

function enotfound(){
    header('HTTP/1.0 404 Not Found');
    die('<!DOCTYPE html><html lang="en">
        <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width,initial-scale=1,minimum-scale=1" name="viewport"/>
        <title>Not found</title>
        </head>
        <body>
            <h1><center>This was not found.</center></h1>
        </body>
        </html>
    ');
}

function make_gallery() {
    if(file_exists('gallery.html')) {
        return True;
    }

    $flickr = json_decode(file_get_contents('.flickr.json'), TRUE);
    $flickr['method'] = "flickr.photosets.getPhotos";
    $flickr['extras'] = "media,url_m,url_l";
    $flickr['per_page'] = "500";
    $flickr['format'] = "json";
    $flickr['nojsoncallback'] = "1";
    $url = sprintf('https://www.flickr.com/services/rest/?%s',http_build_query($flickr));
    $r = json_decode(file_get_contents($url), TRUE);
    $photos = array();
    foreach($r['photoset']['photo'] as $photo) {
        if($photo['media'] != 'photo') {
            continue;
        }
        $et = sprintf(
            '<a class="jgallink" href="%s"><img class="jgalimg" src="%s" width="%s" height="%s" alt="%s" /><div class="caption">%s</div></a>',
            $photo['url_l'], // href
            $photo['url_m'], // src
            $photo['width_m'], // width
            $photo['height_m'], // height
            '', // alt
            $photo['title'] // caption
        );
        array_push($photos, $et);
    }

    $gs = '
    <script src="jquery-3.4.1.min.js"></script>
    <script src="jquery.justifiedGallery.min.js"></script>
    <!--<script src="jquery.magnific-popup.min.js"></script>-->

    <script>
    jQuery("#justified-gallery").justifiedGallery({
        margins: 2,
        captions: false,
        rowHeight: 200,
        lastRow: "center",
        cssAnimation: true,
        imagesAnimationDuration: 1,
        waitThumbnailsLoad: false,
        target: "_blank"
    });
    </script>
    <script>
    /*
    jQuery("#justified-gallery").magnificPopup({
        delegate: \'a\',
        type: \'image\',
        tLoading: \'Loading image #%curr%...\',
        mainClass: \'mfp-img-mobile\',
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0,1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: \'<a href="%url%">The image #%curr%</a> could not be loaded.\',
            titleSrc: function(item) {
                return item.el.attr(\'caption\');
            }
        }
    });
    */
    </script>
    ';
    $t = sprintf(
        '<div id="justified-gallery" class="jgal">%s</div>%s',
        join("\n", $photos),
        $gs
    );
    file_put_contents('gallery.html', $t);
}

class Page {
    public $name;
    private $html;
    private $json;
    private $inserts;

    public function __construct($name) {
        $this->name = $name;
        $this->html = sprintf("page/%s.html", $name);
        $this->json = sprintf("page/%s.json", $name);
        $this->inserts = array(
            'INSERT_GALLERY_HERE' => 'gallery.html'
        );
    }

    public function exists() {
        if (file_exists($this->html) && file_exists($this->json)) {
            return True;
        }
        enotfound();
    }

    public function meta() {
        global $org;
        global $site;

        $stat = stat($self->html);
        $meta = json_decode(file_get_contents($self->json), TRUE);
        print_r($meta);
        $meta['author'] = &$org;
        $meta['publisher'] = &$org;
        $meta['dateModified'] = date("c", $stat['mtime']);
        $meta['datePublished'] = date("c", $stat['ctime']);
        $meta['copyrightYear'] = date("Y", $stat['mtime']);
        $meta['url'] = ('index' == $maybe) ? $site['url'] : sprintf("%s?%s", $site['url'], $maybe);
        $meta['@id'] = $meta['url'];
        $meta['mainEntityOfPage'] = sprintf("%s#main", $meta['url']);
        if (!isset($meta['image']) or empty($meta['image'])) {
            $meta['image'] = $site['image'];
        }
        return $meta;
    }

    public function content(){
        $c = file_get_contents($this->html);
        foreach($this->inserts as $insert => $fname ) {
            if (file_exists($fname)) {
                $c = str_replace($insert, file_get_contents($fname), $c);
            }
        }
        return $c;
    }
}

/* --- */
if(empty($_GET)) {
    $maybe = 'index';
}
else {
    $maybe = array_pop(array_keys($_GET));
}

$org = json_decode(file_get_contents('org.json'), TRUE);
$site = json_decode(file_get_contents('site.json'), TRUE);
$site['author'] = &$org;
$site['publisher'] = &$org;
$site['url'] = sprintf("%s/", rtrim($site['url'], '/'));
make_gallery();

$page = new Page($maybe);
$page->exists();
$meta = $page->meta();
$content = $page->content();
$site['mainEntity'] = &$meta;
/* --- */


?><html lang="<?php echo($meta['inLanguage']); ?>"><head>
    <!--[if lt IE 9]>
    <script src="html5shiv-printshiv.min.js"></script>
    <![endif]-->
    <title><?php echo($meta['headline']); ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1" />
    <meta name="author" content="<?php echo($org['name']) ?>" />
    <link rel="icon" href="<?php echo($site['image']); ?>" />
    <link rel="shortcut icon" type="image/png" href="<?php echo($site['image']); ?>" />
    <link rel="canonical" href="<?php echo($meta['url']); ?>" />
    <meta name="google-site-verification" content="ERSaggz54vqmGW679dGszh79X7lO51I2jNQhY30-oEg" />
    <meta name="description" content="<?php echo($meta['description']); ?>" />
    <meta name="keywords" content="<?php echo($meta['keywords']); ?>" />
    <meta property="og:title" content="<?php echo($meta['headline']); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo($meta['url']); ?>" />
    <meta property="og:description" content="<?php echo($meta['description']); ?>" />
    <meta property="og:site_name" content="<?php echo($site['name']); ?>" />
    <meta property="og:image" content="<?php echo($site['image']) ?>" />
    <meta property="article:published_time" content="<?php echo($meta['datePublished']); ?>">
    <meta property="article:modified_time" content="<?php echo($meta['dateModified']); ?>">
    <meta property="article:author" content="<?php echo($org['name']); ?>">
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="justifiedGallery.min.css" />
    <link rel="stylesheet" href="magnific-popup.css" />
    <script type="application/ld+json">
        <?php echo json_encode($site, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
    </script>

</head>
<body>
    <header>
        <section>
            <svg width="381" height="159"><use xlink:href="#pakua-logo" /></svg>
            <input type="checkbox" id="menu">
            <label for="menu">☰</label>
            <nav>

                <ul>
                    <li>
                        <a href="?index" <?php maybe_active_menu('index') ?>>Home</a>
                    </li>
                    <li>
                        <a href="?the-school" <?php maybe_active_menu('the-school') ?>>What is Pa-Kua?</a>
                    </li>
                    <li>
                        <a href="?timetable" <?php maybe_active_menu('timetable') ?>>Timetable &amp; prices</a>
                    </li>
                    <li>
                        <a href="<?php foreach($site['sameAs'] as $same) { if (stristr($same, 'eventbrite')){ echo($same); } } ?>">Book a trial</a>
                    </li>
                    <li>
                        <a href="?about-us" <?php maybe_active_menu('about-us') ?>>About us</a>
                    </li>
                    <li>
                        <a href="?contact" <?php maybe_active_menu('contact') ?>>Contact</a>
                    </li>
                </ul>
            </nav>
        </section>
    </header>

    <main>

    <?php echo($content); ?>

    </main>

    <footer>
        <section>
            <div>
                <h3>Pa-Kua Resources</h3>
                <ul>
                    <li><a href="https://europa.pakua.com/">Special Class and Itinerancy Payments</a></li>
                    <li><a href="http://pakua.com/">Pa-Kua International Website</a></li>
                    <li><a href="https://www.pakuastore.com/">Pa-Kua Accessories Store</a></li>
                </ul>
            </div>

            <div>
                <h3>Contact</h3>
                <ul>
                    <li><a href="mailto:info@pakuauk.com">info@pakuauk.com</a></li>
                    <li><a href="https://chat.whatsapp.com/CmDShQzb0n021LpO0hqEQ6">Pa-Kua on WhatsApp</a></li>
                    <li><a href="http://webchat.freenode.net/?channels=%23PaKua">#PaKua @ freenode IRC</a></li>
                </ul>
            </div>

            <div>
                <a href="http://pakua.com">
                    <svg width="381" height="138"><use xlink:href="#pakua-logo-white" /></svg>
                </a>

                <nav>
                    <ul>
                        <li>
                            <a href="http://www.facebook.com/PaKuaUK">
                                <svg width="48" height="48"><use xlink:href="#icon-facebook" /></svg>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.twitter.com/pakuauk">
                                <svg width="48" height="48"><use xlink:href="#icon-twitter" /></svg>
                            </a>
                        </li>
                        <li>
                            <a href="https://www.youtube.com/pakuauk">
                                <svg width="48" height="48"><use xlink:href="#icon-youtube" /></svg>
                            </a>
                        </li>
                        <li>
                            <a href="https://instagram.com/pakuauk">
                                <svg width="48" height="48"><use xlink:href="#icon-instagram" /></svg>
                            </a>
                        </li>
                        <li>
                            <a href="https://chat.whatsapp.com/CmDShQzb0n021LpO0hqEQ6">
                                <svg width="48" height="48"><use xlink:href="#icon-whatsapp" /></svg>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </section>
        <section><span>© 2008-2019 by Pa-Kua School UK</span></section>
    </footer>
    <?php include('symbols.svg'); ?>
</body></html>
