<?php

namespace Zver {

    class ClamAV
    {

        protected static $cleanRegexps = [];

        public static function addCleanRegexp($regexp)
        {
            static::$cleanRegexps[] = $regexp;
        }

        public static function cleanCleanRegexps()
        {
            static::$cleanRegexps = [];
        }

        public static function getCleanRegexps()
        {
            return static::$cleanRegexps;
        }

        public static function scan($filename)
        {
            return static::isClean($filename);
        }

        public static function isClean($fileName)
        {
            if (static::isClamScanInstalled() && static::isFreshClamInstalled()) {

                clearstatcache(true);

                if (file_exists($fileName)) {

                    $options = [
                        '--disable-cache',
                        '--no-summary',
                        '--nocerts',
                        '--stdout',
                        '--detect-pua=no',
                        '--detect-structured=no',
                        '--remove=no',
                        '--disable-pe-stats=no',
                    ];

                    $output = StringHelper::load(Common::executeInSystem('clamscan ' . implode(' ', $options) . ' "' . $fileName . '"'));

                    if ($output->getClone()
                               ->trimSpacesRight()
                               ->trimSpacesLeft()
                               ->isEndsWithIgnoreCase(': ok')
                    ) {
                        return true;
                    } else {

                        foreach (static::$cleanRegexps as $regexp) {
                            if (preg_match($regexp, $output->get()) === 1) {
                                return true;
                            }
                        }

                    }

                    return $output->get();

                }
            }

            return false;

        }

        public static function update()
        {
            if (static::isClamScanInstalled() && static::isFreshClamInstalled()) {
                Common::executeInSystem('freshclam');
            }
        }

        public static function isClamScanInstalled()
        {
            return preg_match('/\d+\.\d+\.\d+/i', trim(shell_exec('clamscan --version'))) === 1;
        }

        public static function isFreshClamInstalled()
        {
            return preg_match('/\d+\.\d+\.\d+/i', trim(shell_exec('freshclam --version'))) === 1;
        }

    }

}