<?php
/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    public function test()
    {
        $this->taskPhpUnit('./phpunit.phar')
            ->configFile('phpunit.xml.dist')
            ->run();
    }

    public function phpab()
    {
        $this->_exec('php phpab.phar -o src/autoload.php -e autoload.php src');
    }

    public function dev()
    {
        $this->taskWatch()
            ->monitor('src', function () {
                $this->phpab();
            })
            ->run();
    }
}