<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */

/**
 * PHP version 5
 *
 * Copyright (c) 2008 KUBO Atsuhiro <iteman@users.sourceforge.net>,
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Stagehand_Autoload
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    SVN: $Id$
 * @since      File available since Release 0.1.0
 */

namespace Stagehand;

// {{{ Autoload

/**
 * A class loader for classes with Piece, Stagehand, and user-defined namespaces.
 *
 * @package    Stagehand_Autoload
 * @copyright  2008 KUBO Atsuhiro <iteman@users.sourceforge.net>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License (revised)
 * @version    Release: @package_version@
 * @since      File available since Release 0.1.0
 */
class Autoload
{

    // {{{ properties

    /**#@+
     * @access public
     */

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    private static $_namespaces = array('Piece', 'Stagehand');

    /**#@-*/

    /**#@+
     * @access public
     */

    // }}}
    // {{{ load()

    /**
     * Loads an appropriate class.
     *
     * @param string $class
     */
    public static function load($class)
    {
        do {
            foreach (self::$_namespaces as $namespace) {
                if (preg_match("/^$namespace::/", $class)) {
                    break 2;
                }
            }

            return false;
        } while (false);

        $file = str_replace('::', '/', str_replace('.', '', $class)) . '.php';
        $fp = @fopen($file, 'r', true);
        if ($fp) {
            fclose($fp);
            require $file;
            if (!class_exists($class, false) && !interface_exists($class, false)) {
                die(new Exception("Class $class was not present in $file, (include_path=\"" .
                                  get_include_path() . '")')
                    );
            }

            return true;
        }

        $e = new Exception("Class $class could not be loaded from $file, file does not exist (include_path=\"" .
                           get_include_path() . '")'
                           );
        $trace = $e->getTrace();
        if (isset($trace[2])
            && isset($trace[2]['function'])
            && in_array($trace[2]['function'], array('class_exists', 'interface_exists'))
            ) {
            return false;
        }

        if (isset($trace[1])
            && isset($trace[1]['function'])
            && in_array($trace[1]['function'], array('class_exists', 'interface_exists'))
            ) {
            return false;
        }

        die((string)$e);
    }

    // }}}
    // {{{ addNamespace()

    /**
     * Adds a namespace to the targets for autoloading.
     *
     * @param string $namespace
     */
    public static function addNamespace($namespace)
    {
        if (array_search($namespace, self::$_namespaces) !== false) {
            return;
        }

        self::$_namespaces[] = $namespace;
    }

    // }}}
    // {{{ removeNamespace()

    /**
     * Removes a namespace from the targets for autoloading.
     *
     * @param string $namespace
     */
    public static function removeNamespace($namespace)
    {
        $index = array_search($namespace, self::$_namespaces);
        if ($index === false) {
            return;
        }

        array_splice(self::$_namespaces, $index, 1);
    }

    /**#@-*/

    /**#@+
     * @access protected
     */

    /**#@-*/

    /**#@+
     * @access private
     */

    /**#@-*/

    // }}}
}

// }}}

// set up __autoload
if (!($_____t = spl_autoload_functions())
    || !in_array(array('Stagehand::Autoload', 'load'), spl_autoload_functions())
    ) {
    spl_autoload_register(array('Stagehand::Autoload', 'load'));
    if (function_exists('__autoload') && ($_____t === false)) {

        // __autoload() was being used, but now would be ignored, add
        // it to the autoload stack
        spl_autoload_register('__autoload');
    }
}

unset($_____t);

// set up include_path if it doesn't register our current location
$____paths = explode(PATH_SEPARATOR, get_include_path());
$____found = false;
foreach ($____paths as $____path) {
    if ($____path == dirname(dirname(__FILE__))) {
        $____found = true;
        break;
    }
}

if (!$____found) {
    set_include_path(get_include_path() . PATH_SEPARATOR . dirname(dirname(__FILE__)));
}

unset($____paths);
unset($____path);
unset($____found);

/*
 * Local Variables:
 * mode: php
 * coding: iso-8859-1
 * tab-width: 4
 * c-basic-offset: 4
 * c-hanging-comment-ender-p: nil
 * indent-tabs-mode: nil
 * End:
 */
