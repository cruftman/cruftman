<?php
/**
 * @file docs/sphinx/Behat/DocExampleContext.php
 *
 * This file is part of the Korowai package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\docs
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Docs\Behat;

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

use Korowai\Lib\Ldap\Behat\ExtLdapContext;

class DocExampleContext implements Context
{
    protected $stdout = null;
    protected $stderr = null;
    protected $status = null;

    protected function getExamplePath(string $file)
    {
        return realpath(__dir__ . '/../examples/' . $file);
    }

    protected function getExampleFileContents(string $file, $default = '')
    {
        $path = $this->getExamplePath($file);
        if($path === false || !file_exists($path)) {
            return $default;
        }
        return file_get_contents($path);
    }

    protected function getExampleCmd(string $file)
    {
        $path = $this->getExamplePath($file);
        return implode(" ", [PHP_BINARY, "-d auto_prepend_file=vendor/autoload.php", $path]);
    }

    protected function runDocExample(string $file)
    {
        $descriptorspec = [
            0 => ["pipe", "r"],
            1 => ["pipe", "w"],
            2 => ["pipe", "w"]
        ];
        $cmd = $this->getExampleCmd($file);
        $cwd = realpath(__dir__ . "/../../..");

        $proc = proc_open($cmd, $descriptorspec, $pipes, $cwd);

        if (!is_resource($proc)) {
            throw \RuntimeException("could not execute example: $file");
        }

        fclose($pipes[0]);

        $this->stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $this->stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $this->status = proc_close($proc);
    }

    protected function resetExample()
    {
        $this->stdout = null;
        $this->stderr = null;
        $this->status = null;
    }

    /**
     * Clear and initialize data in the ldap database.
     *
     * @BeforeScenario @initLdapDbBeforeScenario
     * @BeforeSuite @initLdapDbBeforeSuite
     * @BeforeFeature @initLdapDbBeforeFeature
     * @AfterScenario @initLdapDbAfterScenario
     * @AfterSuite @initLdapDbAfterSuite
     * @AfterFeature @initLdapDbAfterFeature
     */
    public static function initLdapDb()
    {
        ExtLdapContext::initDb();
    }

    /**
     * @When I executed doc example :file
     */
    public function iExecutedDocExample(string $file)
    {
        $this->resetExample();
        $this->runDocExample($file);
    }

    /**
     * @Then I should see stdout from :file
     */
    public function iShouldSeeStdoutFrom(?string $file = null)
    {
        if (!is_null($file)) {
            Assert::assertEquals($this->getExampleFileContents($file), $this->stdout);
        }
    }

    /**
     * @Then I should see stderr from :file
     */
    public function iShouldSeeStderrFrom(?string $file = null)
    {
        if (!is_null($file)) {
            Assert::assertEquals($this->getExampleFileContents($file), $this->stderr);
        }
    }

    /**
     * @Then I should see exit code :code
     */
    public function iShouldSeeExitCode(int $code)
    {
        Assert::assertEquals($code, $this->status);
    }
}

// vim: syntax=php sw=4 ts=4 et:
