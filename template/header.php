<?php

global $PAGE;
global $SITE;
global $ORG;
global $NAVIGATION;

?><html lang="<?php echo($PAGE['inLanguage']); ?>"><head>
    <!--[if lt IE 9]>
    <script src="html5shiv-printshiv.min.js"></script>
    <![endif]-->
    <title><?php echo($PAGE['headline']); ?></title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1" />
    <meta name="author" content="<?php echo($ORG['name']) ?>" />
    <link rel="icon" href="<?php echo($SITE['image']); ?>" />
    <link rel="shortcut icon" type="image/png" href="<?php echo($SITE['image']); ?>" />
    <link rel="canonical" href="<?php echo($PAGE['url']); ?>" />
    <meta name="description" content="<?php echo($PAGE['description']); ?>" />
    <meta name="keywords" content="<?php echo(implode(",", $PAGE['keywords'])); ?>" />
    <meta property="og:title" content="<?php echo($PAGE['headline']); ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="<?php echo($PAGE['url']); ?>" />
    <meta property="og:description" content="<?php echo($PAGE['description']); ?>" />
    <meta property="og:site_name" content="<?php echo($SITE['name']); ?>" />
    <meta property="og:image" content="<?php echo($SITE['image']) ?>" />
    <meta property="article:published_time" content="<?php echo($PAGE['datePublished']); ?>">
    <meta property="article:modified_time" content="<?php echo($PAGE['dateModified']); ?>">
    <meta property="article:author" content="<?php echo($ORG['name']); ?>">
    <link rel="stylesheet" href="style.css" />
    <link rel="stylesheet" href="justifiedGallery.min.css" />
    <link rel="stylesheet" href="magnific-popup.css" />
    <script type="application/ld+json">
        <?php echo json_encode($SITE, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
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
                    <?php
                        foreach($NAVIGATION as $navname => $navmeta) {
                    ?>
                        <li>
                            <a href="<?php echo(ltrim(str_replace($SITE['url'], '', $navmeta['url']), '/')); ?>"<?php if($PAGE['identifier'] == $navname) { echo(' class="active"'); } ?> ?><?php echo($navmeta['name']); ?></a>
                        </li>
                    <?php
                        }
                    ?>
                </ul>
            </nav>
        </section>
    </header>

    <main class="h-entry hentry">
