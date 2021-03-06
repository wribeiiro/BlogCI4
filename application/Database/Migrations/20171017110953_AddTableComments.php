<?php

/*
 * BlogCI4 - Blog write with Codeigniter v4dev
 * @author Deathart <contact@deathart.fr>
 * @copyright Copyright (c) 2018 Deathart
 * @license https://opensource.org/licenses/MIT MIT License
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Class Migration_AddTableComments
 *
 * @package App\Database\Migrations
 */
class Migration_AddTableComments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'post_id' => ['type' => 'INT', 'constraint' => 11],
            'author_name' => ['type' => 'VARCHAR', 'constraint' => 250],
            'author_email' => ['type' => 'TEXT'],
            'author_ip' => ['type' => 'VARCHAR', 'constraint' => 250]
        ]);

        $this->forge->addField('created_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP');

        $this->forge->addField([
            'verified' => ['type' => 'INT', 'constraint' => 11, 'default' => '0'],
            'content' => ['type' => 'LONGTEXT'],
        ]);

        $this->forge->addPrimaryKey('id');

        $this->forge->createTable('comments');
    }

    //--------------------------------------------------------------------

    public function down()
    {
        $this->forge->dropTable('comments');
    }
}
