<?php

class Create_salita_quest_tables {

    private $_lava;

    public function __construct()
    {
        $this->_lava = lava_instance();
        $this->_lava->call->dbforge();
    }

    public function up()
    {
        $this->_lava->dbforge
            ->add_field([
                'id'          => ['type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'null' => FALSE],
                'region'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE],
                'word'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => FALSE],
                'translation' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => FALSE],
                'example'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE],
                'audio_url'   => ['type' => 'VARCHAR', 'constraint' => 500, 'null' => TRUE],
                'status'      => ['type' => 'ENUM', 'constraint' => "'pending','approved','rejected'", 'null' => FALSE, 'default' => 'pending'],
                'submitted_by'=> ['type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE],
                'created_at'  => ['type' => 'DATETIME', 'null' => FALSE, 'default' => 'CURRENT_TIMESTAMP'],
            ])
            ->add_key('id', primary: TRUE)
            ->add_key('region', name: 'sq_region_idx')
            ->add_key('status', name: 'sq_status_idx')
            ->create_table('sq_words');

        $this->_lava->dbforge
            ->add_field([
                'id'        => ['type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE, 'null' => FALSE],
                'user_id'   => ['type' => 'INT', 'unsigned' => TRUE, 'null' => FALSE],
                'region'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE],
                'game'      => ['type' => 'ENUM', 'constraint' => "'matching','listening'", 'null' => FALSE],
                'points'    => ['type' => 'INT', 'unsigned' => TRUE, 'null' => FALSE, 'default' => 0],
                'completed_at' => ['type' => 'DATETIME', 'null' => FALSE, 'default' => 'CURRENT_TIMESTAMP'],
            ])
            ->add_key('id', primary: TRUE)
            ->add_key(['user_id', 'region'], name: 'sq_user_region_idx')
            ->create_table('sq_progress');
    }

    public function down()
    {
        $this->_lava->dbforge->drop_table('sq_progress');
        $this->_lava->dbforge->drop_table('sq_words');
    }
}
