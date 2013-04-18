<?php

namespace Taskul\TimelineBundle\Spread;

use Spy\Timeline\Model\ActionInterface;
use Spy\Timeline\Spread\SpreadInterface;
use Spy\Timeline\Spread\Entry\EntryCollection;
use Spy\Timeline\Spread\Entry\Entry;
use Spy\Timeline\Spread\Entry\EntryUnaware;

class Spread implements SpreadInterface
{
    CONST TASK_CLASS = 'Taskul\TaskBundle\Entity\Task';

    public function supports(ActionInterface $action)
    {
        return true; //or false, you can look at timeline action to make your decision
    }

    public function process(ActionInterface $action, EntryCollection $coll)
    {
        // can define an Entry with a ComponentInterface as argument
        $coll->add(new Entry($action->getComponent('subject')));

        // or an EntryUnware, on these examples, we are not aware about components and
        // we don't want to retrieve them, let bundle do that for us.

        // composite key
        $coll->add(new EntryUnaware('model', array('1', '2')));
        $coll->add(new EntryUnaware('some\othermodel', 1));
        $coll->add(new EntryUnaware('othermodel', 'aodadoa'), 'CUSTOM_CONTEXT');
    }
}