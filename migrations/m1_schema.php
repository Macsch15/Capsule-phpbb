<?php
namespace macsch15\capsule\migrations;

class m1_schema extends \phpbb\db\migration\migration
{
	/**
	* Add table schema to the database:
    *
	* @return array Array of table schema
	* @access public
	*/
	public function update_schema()
	{
		return [
			'add_tables'	=> [
				$this->table_prefix . 'capsule'	=> [
					'COLUMNS'	=> [
						'img_id'						=> ['UINT', null, 'auto_increment'],
						'img_user_id'					=> ['UINT', 0],
						'img_filename'					=> ['TEXT_UNI', 0],
                        'img_uploaded_time'             => ['UINT:10', 0],
                        'img_size'                      => ['UINT:10', 0]
					],
					'PRIMARY_KEY'	=> 'img_id',
				],
			],
		];
	}

	/**
	* Drop table schema from the database
	*
	* @return array Array of table schema
	* @access public
	*/
	public function revert_schema()
	{
		return [
			'drop_tables'	=> [
				$this->table_prefix . 'capsule',
			]
		];
	}
}
