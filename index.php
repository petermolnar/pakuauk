<?php

define('TEMPLATES', sprintf("%s%s", "template", DIRECTORY_SEPARATOR));
define('DATADIR', sprintf("%s%s", "data", DIRECTORY_SEPARATOR));
define('PAGEDIR', sprintf("%s%s", "page", DIRECTORY_SEPARATOR));
define('ORG_JSON', sprintf("%s%s", DATADIR, 'organisation.json'));
define('SITE_JSON', sprintf("%s%s", DATADIR, 'site.json'));
define('SOCIAL_JSON', sprintf("%s%s", DATADIR, 'social.json'));
define('RESOURCES_JSON', sprintf("%s%s", DATADIR, 'resources.json'));
define('CONTACT_JSON', sprintf("%s%s", DATADIR, 'contact.json'));
define('NAV_JSON', sprintf("%s%s", DATADIR, 'navigation.json'));

define('HEADER_TMPL', sprintf("%s%s", TEMPLATES, 'header.php'));
define('FOOTER_TMPL', sprintf("%s%s", TEMPLATES, 'footer.php'));

class Page {
    public $name;
    public $meta;
    private $nav;
    private $targetfile;
    private $sourcefile;
    private $external;

    public function __construct($name, $meta) {
        global $SITE;
        global $NAVIGATION;
        $this->name = $name;
        $this->targetfile = sprintf("%s.html", $this->name);
        $this->sourcefile = sprintf("%s%s.php", TEMPLATES, $this->name);
        $stat = array(
            'mtime' => time(),
            'ctime' => time()
        );
        if(is_file($this->sourcefile)) {
            $stat = stat($this->sourcefile);
        }
        $defaults = array(
            "@context" => "http=>//schema.org",
            "@type" => "Article",
            "inLanguage" => "en",
            "keywords" => $SITE['keywords'],
            "author" => &$SITE['author'],
            "publisher" => &$SITE['publisher'],
            "dateModified" => date("c", $stat['mtime']),
            "datePublished" => date("c", $stat['ctime']),
            "copyrightYear" => date("Y", $stat['mtime']),
            "identifier" => $name
        );
        $this->meta = array_merge($defaults, $meta);
        if(!isset($this->meta['url'])) {
            $this->meta["url"] = sprintf("%s/%s.html", $SITE['url'], $this->name);
            $this->meta['mainEntityOfPage'] = sprintf("%s#main", $this->meta['url']);
            $this->external = False;
        }
        else {
            $this->external = True;
        }

    }

    public function render() {
        ob_start();

        global $PAGE;
        $PAGE = $this->meta;
        include(HEADER_TMPL);
        $f = sprintf("%s%s.html", PAGEDIR, $this->name);
        if (file_exists($f)) {
            include($f);
        }
        include(FOOTER_TMPL);

        $r = ob_get_clean();
        return $r;
    }

    public function save() {
        if (!$this->external) {
            file_put_contents($this->targetfile, $this->render());
        }
    }
}

$ORG = json_decode(file_get_contents(ORG_JSON), TRUE);

$SITE = json_decode(file_get_contents(SITE_JSON), TRUE);
$SITE['author'] = &$ORG;
$SITE['publisher'] = &$ORG;
$SITE['url'] = sprintf("%s/", rtrim($SITE['url'], '/'));

$SOCIAL = json_decode(file_get_contents(SOCIAL_JSON), TRUE);
$RESOURCES = json_decode(file_get_contents(RESOURCES_JSON), TRUE);
$CONTACT = json_decode(file_get_contents(CONTACT_JSON), TRUE);

$NAVIGATION = json_decode(file_get_contents(NAV_JSON), TRUE);
$build = array();

foreach($NAVIGATION as $navname => $navmeta) {
    $e = new Page($navname, $navmeta);
    $NAVIGATION[$navname] = &$e->meta;
    array_push($build, $e);
}

foreach($build as $e) {
    $e->save();
}
