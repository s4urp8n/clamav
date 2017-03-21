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

        public static function isClean($fileName)
        {
            static::checkInstallation();
            static::update();

            if (file_exists($fileName)) {

                $output = StringHelper::load(Common::executeInSystem('clamscan --no-summary "' . $fileName . '"'));

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

            return false;

        }

        public static function update()
        {
            static::checkInstallation();

            Common::executeInSystem('freshclam');
        }

        protected static function checkInstallation()
        {
            if (!static::isClamScanInstalled() || !static::isFreshClamInstalled()) {
                throw  new \Exception('Freshclam or Clamscan is not installed!');
            }
        }

        public static function isClamScanInstalled()
        {
            return preg_match('/^clamav\s+\d+\.\d+\.\d+/i', trim(shell_exec('clamscan --version'))) === 1;
        }

        public static function isFreshClamInstalled()
        {
            return preg_match('/^clamav\s+\d+\.\d+\.\d+/i', trim(shell_exec('freshclam --version'))) === 1;
        }

    }

}