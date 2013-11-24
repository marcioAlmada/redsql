<?php

use RedBean_Facade as R;

# loading redsql into R
R::ext( 'redsql', function ($type) {
    return new RedBeanPHP\Plugins\RedSql\Finder($type);
});
