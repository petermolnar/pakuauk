<?php

global $SOCIAL;
global $RESOURCES;
global $CONTACT;
global $SITE;

?>
</main>

    <footer class="p-author h-card vcard">
        <section>
            <div>
                <h3>Pa-Kua Resources</h3>
                <ul>
                    <?php foreach($RESOURCES as $e) { ?>
                    <li><a href="<?php echo($e['url']); ?>"><?php echo($e['title']); ?></a></li>
                    <?php } ?>
                </ul>
            </div>

            <div>
                <h3>Contact</h3>
                <ul>
                    <?php foreach($CONTACT as $e) { ?>
                    <li><a href="<?php echo($e['url']); ?>"><?php echo($e['title']); ?></a></li>
                    <?php } ?>
                </ul>
            </div>

            <div>
                <a href="https://pakuauk.com">
                    <svg class="u-photo photo" width="381" height="138"><use xlink:href="#pakua-logo-white" /></svg>
                </a>

                <nav>
                    <ul>
                        <?php foreach($SOCIAL as $e) { ?>
                        <li>
                            <a href="<?php echo($e['url']); ?>" rel="me">
                                <svg width="48" height="48"><use xlink:href="#icon-<?php echo($e['name']);?>" /></svg>
                            </a>
                        </li>
                    <?php } ?>
                    </ul>
                </nav>
            </div>
        </section>
        <section><p>© 2008-2019 by <a href="<?php echo($SITE['url']); ?>" class="p-name u-url fn url" rel="me"><?php echo($SITE['headline']); ?></a></p></section>
    </footer>
    <?php include('symbols.svg'); ?>
</body></html>
