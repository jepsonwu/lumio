<?php

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

/**
 * Defines application features from the specific context.
 */
class FeatureContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct()
    {
    }

    /**
     * @Given the following people exist:
     */
    public function theFollowingPeopleExist(TableNode $table)
    {
        throw new PendingException();
    }

    /**
     * @Given some step with :arg1 argument
     */
    public function someStepWithArgument($arg1)
    {
        throw new PendingException();
    }

    /**
     * @Given number step with :arg1
     */
    public function numberStepWith($arg1)
    {
        throw new PendingException();
    }
}
