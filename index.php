<?php

 /*
  *
  * RevolveR 
  *
  * Contents Management Framework
  *
  * v.1.9.0
  * 
  *
  *
  *
  *
  *                   ^
  *                  | |
  *                @#####@
  *              (###   ###)-.
  *            .(###     ###) \
  *           /  (###   ###)   )
  *          (=-  .@#####@|_--"
  *          /\    \_|l|_/ (\
  *         (=-\     |l|    /
  *          \  \.___|l|___/
  *          /\      |_|   /
  *         (=-\._________/\
  *          \             /
  *            \._________/
  *              #  ----  #
  *              #   __   #
  *              \########/
  *
  *
  *
  * Developer: Dmitry Maltsev
  *
  * License: Apache 2.0
  *
  */

#
# Allow strict
#
# To run framework with developers
# use strict_types = 1 bellow
#

declare( ticks = 0, strict_types = 0 );

define('StartTime', microtime(true));

define('MemoryStart', memory_get_usage());

// Countries and languages
require_once('./private/Countries.php');
require_once('./private/Translations.php');

// Structures
require_once('./Kernel/Structures/DataBase.php');

// Helpers
require_once('./Kernel/Modules/HTMLFormBuilder.php');
require_once('./Kernel/Modules/Calendar.php');
require_once('./Kernel/Modules/DetectUserAgent.php');

// Modules
require_once('./Kernel/Modules/Notifications.php');
require_once('./Kernel/Modules/Parse.php');
require_once('./Kernel/Modules/Markup.php');
require_once('./Kernel/Modules/Minifier.php');

require_once('./Kernel/Modules/Language.php');
require_once('./Kernel/Modules/Captcha.php');
require_once('./Kernel/Modules/DataBaseX.php');
require_once('./Kernel/Modules/Cipher.php');

require_once('./Kernel/Modules/Auth.php');
require_once('./Kernel/Modules/Menu.php');
require_once('./Kernel/Modules/Route.php');
require_once('./Kernel/Modules/Node.php');
require_once('./Kernel/Modules/Vars.php');
require_once('./Kernel/Modules/Mail.php');

// Models
require_once('./Kernel/Modules/Model.php');

// Attendance write
require_once('./Kernel/Modules/Statistics.php');

// MMDB support
require_once('./Kernel/Modules/Extra/MMDBDecoder.php');
require_once('./Kernel/Modules/Extra/MMDBReader.php');

// Conclude
require_once('./Kernel/Modules/Conclude.php');

// Kernel Initialize :: [ Run Framework with Strict ]
require_once('./Kernel/Kernel.php');

?>
