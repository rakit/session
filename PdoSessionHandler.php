<?php

namespace Rakit\Session;

use SessionHandlerInterface;
use PDO;

class PdoSessionHandler implements SessionHandlerInterface {

    protected $connection;

    protected $tablename;

    protected $configs;

	public function __construct(PDO $connection, $tablename = 'sessions', array $configs = array())
	{
        $this->connection = $connection;
        $this->tablename = $tablename;
        $this->configs = array_merge(array(
            'col_sessid' => 'sess_id',
            'col_data' => 'sess_data',
            'col_last_activity' => 'last_activity'
        ), $configs);
	}

	public function open($save_path, $sess_name)
	{
	    return true;
	}

	public function read($sess_id)
	{
        $table = $this->tablename;
        $col_sessid = $this->configs['col_sessid'];
        $col_data = $this->configs['col_data'];
		$query = $this->connection->query("
            SELECT * FROM {$table} 
            WHERE {$col_sessid} = '$sess_id' 
            LIMIT 1
        ");

        $session = $query->fetch(PDO::FETCH_ASSOC);

        $this->exists = (false === empty($session));
        
        return $this->exists? base64_decode($session[$col_data]) : '';
    }

	public function write($sess_id, $data)
	{
        $table = $this->tablename;
        $col_sessid = $this->configs['col_sessid'];
        $col_data = $this->configs['col_data'];
        $col_la = $this->configs['col_last_activity'];

        $data = base64_encode($data);
        $time = time();

        if($this->exists) {
            $this->connection->query("
                UPDATE {$table} 
                SET 
                    {$col_data} = '{$data}',
                    {$col_la} = {$time}
                WHERE {$col_sessid} = '{$sess_id}'
            ");
        } else {
            $this->connection->query("
                INSERT INTO {$table}
                ({$col_sessid}, {$col_data}, {$col_la})
                VALUES ('{$sess_id}','{$data}', {$time})
            ");
        }

        $this->exists = true;
	}

	public function close()
	{
		return true;
	}

	public function destroy($sess_id)
	{
        $table = $this->table;
        $col_sessid = $this->configs['col_sessid'];

        $this->connection->query("DELETE FROM {$table} WHERE {$col_sessid}='{$sess_id}'");

        return true;
	}

	public function gc($lifetime)
	{
        $table = $this->table;
        $col_la = $this->configs['col_last_activity'];
        $expired_time = time() - $lifetime;

        $this->connection->query("DELETE FROM {$table} WHERE {$col_la} <= {$expired_time}");

        return true;
	}


}
