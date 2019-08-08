<?php

use Phinx\Migration\AbstractMigration;

class MyNewMigration extends AbstractMigration
{

    public function change()
    {
        $guestList = $this->table('guestList');
        $guestList->addColumn('name', 'string', ['limit' => 20])
            ->addColumn('email', 'string', ['limit' => 100])
            ->addColumn('comment', 'string', ['limit' => 200])
            ->addColumn('image', 'string', ['limit' => 100])
            ->addColumn('type', 'string', ['limit' => 30])
            ->addColumn('time', 'datetime')
            ->addColumn('date', 'datetime', ['null' => true])
            ->addIndex(['name', 'email'], ['unique' => true])
            ->save();
    }
}
