<?php

use yii\db\Migration;

/**
 * Handles the creation of table `question`.
 */
class m180214_021149_create_question_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
 
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        
        $this->createTable('question', [
            'id' => $this->primaryKey(),
            'body' => $this->string()->notNull(),
            'created_at' => $this->timestamp()->notNull(),
            'author_id' => $this->integer()->notNull(),
        ], $tableOptions);

        // creates index for column `author_id`
        $this->createIndex(
            'idx-question-author_id',
            'question',
            'author_id'
        );

        // add foreign key for table `user`
        $this->addForeignKey(
            'fk-question-author_id',
            'question',
            'author_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('question');
    }
}
