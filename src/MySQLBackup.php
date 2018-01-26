<?php

namespace Phelium\Component;

/**
 * MySQLBackup
 * 
 * Backup your MySQL databases by selecting tables (or not) and using compression (zip or gzip) !
 * 
 * @author ShevAbam <me@shevarezo.fr>
 * @link https://github.com/shevabam/mysql-backup
 * @license GNU GPL 2.0
 */
class MySQLBackup
{
    /**
     * Database information
     *
     * @var array
     */
    public $db = array(
        'host'      => null,
        'port'      => null,
        'user'      => null,
        'password'  => null,
        'name'      => null,
    );

    /**
     * Database connection link
     *
     * @var null
     */
    private $dbh = null;

    /**
     * Tables list
     *
     * @var array
     */
    public $tables = array();

    /**
     * Excluded tables list
     *
     * @var array
     */
    public $excludedTables = array();

    /**
     * Filename
     *
     * @var string
     */
    public $filename = 'dump';

    /**
     * Filename extension
     *
     * @var string
     */
    public $extension = 'sql';

    /**
     * Is file is delete at the end ?
     *
     * @var boolean
     */
    public $deleteFile = false;

    /**
     * Is file is downloaded automatically ?
     *
     * @var boolean
     */
    public $downloadFile = false;

    /**
     * Compress file format
     *
     * @var null
     */
    public $compressFormat = null;

    /**
     * Available compress formats
     *
     * @var array
     */
    public $compressAvailable = array('zip', 'gz', 'gzip');

    /**
     * Dump table structure ?
     *
     * @var boolean
     */
    public $dumpStructure = true;

    /**
     * Dump table datas ?
     *
     * @var boolean
     */
    public $dumpDatas = true;

    /**
     * Add DROP TABLE IF EXISTS before CREATE TABLE ?
     *
     * @var boolean
     */
    public $addDropTable = true;

    /**
     * Add IF NOT EXISTS in CREATE TABLE statment ?
     *
     * @var boolean
     */
    public $addIfNotExists = true;

    /**
     * Add CREATE DATABASE IF NOT EXISTS ?
     *
     * @var boolean
     */
    public $addCreateDatabaseIfNotExists = true;




    /**
     * Initialization
     *
     * @param string $host     SQL host
     * @param string $user     Username
     * @param string $password Password
     * @param string $db       DB name
     */
    public function __construct($host, $user, $password, $db, $port = 3306)
    {
        $this->db = array(
            'host'      => $host,
            'port'      => $port,
            'user'      => $user,
            'password'  => $password,
            'name'      => $db,
        );

        $this->filename = 'dump_'.$db.'_'.date('Ymd-H\hi');

        // Connection to the database
        $this->databaseConnect();
    }


    /**
     * Database connection link
     */
    private function databaseConnect()
    {
        $dsn = 'mysql:host='.$this->db['host'].';port='.$this->db['port'].';dbname='.$this->db['name'];

        $options = array(
            \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            \PDO::ATTR_PERSISTENT         => true,
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION
        );

        // Create a new PDO instance
        try
        {
            $this->dbh = new \PDO($dsn, $this->db['user'], $this->db['password'], $options);
        }
        // Catch any errors
        catch (\PDOException $e)
        {
            exit($e->getMessage());
        }
    }

    /**
     * Query fetcher
     *
     * @param  string  $q        Query
     * @param  boolean $fetchAll fetchAll or fetch
     * @return object  PDO
     */
    private function query($q, $fetchAll = true)
    {
        $stmt = $this->dbh->query($q);

        if ($fetchAll === true)
        {
            return $stmt->fetchAll();
        }
        else
        {
            return $stmt->fetch();
        }
    }

    
    /**
     * Set filename (default : dump_{db name}_{yymmdd-HHhMM}.sql)
     * 
     * @param  string $name       Filename
     * @return object MySQLBackup
     */
    public function setFilename($name)
    {
        $this->filename = $name;

        return $this;
    }
    
    /**
     * Set download file (default : false)
     * 
     * @param  bool   $p          Allow to download file or not
     * @return object MySQLBackup
     */
    public function setDownload($p)
    {
        $this->downloadFile = $p;

        return $this;
    }
    
    /**
     * Set compress file format (default : null - no compress)
     * 
     * @param  string $p          Compress format available in $this->compressAvailable
     * @return object MySQLBackup
     */
    public function setCompress($p)
    {
        if (in_array($p, $this->compressAvailable))
            $this->compressFormat = $p;

        return $this;
    }
    
    /**
     * Set delete file (default : false)
     * 
     * @param  bool   $p          Allow to delete file or not
     * @return object MySQLBackup
     */
    public function setDelete($p)
    {
        $this->deleteFile = $p;

        return $this;
    }
    
    
    /**
     * Dump the structure ? (default : true)
     * 
     * @param  bool   $p          Dump structure or not
     * @return object MySQLBackup
     */
    public function setDumpStructure($p)
    {
        $this->dumpStructure = $p;

        return $this;
    }
    
    /**
     * Dump the datas ? (default : true)
     * 
     * @param  bool   $p          Dump datas or not
     * @return object MySQLBackup
     */
    public function setDumpDatas($p)
    {
        $this->dumpDatas = $p;

        return $this;
    }
    
    
    /**
     * Add DROP TABLE IF EXISTS before CREATE TABLE statment (default : true)
     * 
     * @param  bool   $p          Add DROP TABLE IF EXISTS or not
     * @return object MySQLBackup
     */
    public function addDropTable($p)
    {
        $this->addDropTable = $p;

        return $this;
    }
    
    
    /**
     * Add "IF NOT EXISTS" after CREATE TABLE statment (default : true)
     * 
     * @param  bool   $p          Add IF NOT EXISTS or not
     * @return object MySQLBackup
     */
    public function addIfNotExists($p)
    {
        $this->addIfNotExists = $p;

        return $this;
    }
    
    
    /**
     * Add "CREATE DATABASE IF NOT EXISTS" (default : true)
     * 
     * @param  bool   $p          Add CREATE DATABASE IF NOT EXISTS or not
     * @return object MySQLBackup
     */
    public function addCreateDatabaseIfNotExists($p)
    {
        $this->addCreateDatabaseIfNotExists = $p;

        return $this;
    }
    
    
    /**
     * Add table name to dump
     *
     * @param  string $table      Table name to dump
     * @return object MySQLBackup
     */
    public function addTable($table)
    {
        if (!in_array($table, $this->tables))
        {
            $this->tables[] = $table;
        }

        return $this;
    }
    
    /**
     * Dump selected tables
     * 
     * @param  array  $tables     Tables to backup
     * @return object MySQLBackup
     */
    public function addTables(array $tables)
    {
        if (is_array($tables) && count($tables) > 0)
        {
            foreach ($tables as $t)
            {
                $this->addTable($t);
            }
        }

        return $this;
    }
    
    /**
     * Dump all tables
     * 
     * @return object MySQLBackup
     */
    public function addAllTables()
    {
        $result = $this->query('SHOW TABLES');

        foreach ($result as $row)
        {
            $this->addTable($row[0]);
        }

        return $this;
    }

    /**
     * Exclude tables
     * 
     * @return object MySQLBackup
     */
    public function excludeTables(array $tables)
    {
        if (is_array($tables) && count($tables) > 0)
        {
            $this->excludedTables = $tables;
        }

        return $this;
    }


    /**
     * Dump SQL database with selected tables
     */
    public function dump()
    {
        $return = '';
        
        if (count($this->tables) == 0)
        {
            $this->addAllTables();
        }
        
        $return .= "--\n";
        $return .= "-- Backup ".$this->db['name']." - ".date('Y-m-d H:i:s')."\n";
        $return .= "--\n\n\n";
        
        $return .= "SET FOREIGN_KEY_CHECKS=0;";
        $return .= "\n\n\n";

        if ($this->addCreateDatabaseIfNotExists === true)
        {
            $return .= "CREATE DATABASE IF NOT EXISTS `".$this->db['name']."`;\n";
            $return .= "USE `".$this->db['name']."`;";
            $return .= "\n\n\n";
        }
        
        foreach ($this->tables as $table)
        {
            // We skip excluded tables
            if (in_array($table, $this->excludedTables))
                continue;

            $stmt = $this->dbh->query('SELECT * FROM `'.$table.'`');
            $stmt->execute();
            $num_fields = $stmt->columnCount();
            
            $return .= "--\n";
            $return .= "-- Table ".$table."\n";
            $return .= "--\n\n";
            
            // Dump structure ?
            if ($this->dumpStructure === true)
            {
                // Add DROP TABLE ?
                if ($this->addDropTable === true)
                {
                    $return .= 'DROP TABLE IF EXISTS `'.$table.'`;';
                    $return .= "\n\n";
                }

                $create_table_q = $this->query('SHOW CREATE TABLE `'.$table.'`', false);
                $create_table = $create_table_q[1];
                
                // Add IF NOT EXISTS ?
                if ($this->addIfNotExists === true)
                {
                    $create_table = preg_replace('/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $create_table);
                }
                
                $return .= $create_table.";\n\n";
            }
            
            // Dump datas ?
            if ($this->dumpDatas === true)
            {
                $datas = $this->query('SELECT * FROM `'.$table.'`');

                foreach ($datas as $row)
                {
                    $return .= 'INSERT INTO `'.$table.'` VALUES(';
                    
                    for ($i = 0; $i < $num_fields; $i++) 
                    {
                        if (isset($row[$i]))
                        {
                            $row[$i] = addslashes($row[$i]);
                            
                            $return .= '"'.$row[$i].'"';
                        }
                        else
                        {
                            $return .= '""';
                        }
                        
                        if ($i < ($num_fields-1))
                        {
                            $return .= ', ';
                        }
                    }
                    
                    $return .= ");\n";
                }
            }
            
            $return .= "\n\n";
            $return .= "-- --------------------------------------------------------";
            $return .= "\n\n\n";
        }
        
        
        // Save content in file
        file_put_contents($this->filename.'.'.$this->extension, $return);
        
        
        // Zip the file ?
        if (!is_null($this->compressFormat))
        {
            $this->compress();
        }
        
        // Download the file ?
        if ($this->downloadFile === true)
        {
            $this->download();
        }
        
        // Delete the file ?
        if ($this->deleteFile === true)
        {
            $this->delete();
        }
    }
    
    
    /**
     * Download the dump file
     */
    private function download()
    {
        header('Content-disposition: attachment; filename="'.$this->filename.'.'.$this->extension.'"');
        header('Content-type: application/octet-stream');
        
        readfile($this->filename.'.'.$this->extension);
    }
    
    
    /**
     * Compress the file
     */
    private function compress()
    {
        switch ($this->compressFormat)
        {
            case 'zip':
                
                if (class_exists('\ZipArchive'))
                {
                    $zip = new \ZipArchive;
                    
                    if ($zip->open($this->filename.'.zip', \ZipArchive::CREATE) === true)
                    {
                        $zip->addFile($this->filename.'.'.$this->extension, basename($this->filename).'.'.$this->extension);
                        $zip->close();
                        
                        // We delete the sql file
                        $this->delete();
                        
                        // Changing file extension to zip
                        $this->extension = 'zip';
                    }
                }
                else
                {
                    throw new \Exception('\ZipArchive object does not exists');
                }
                
            break;
            
            case 'gz':
            case 'gzip':
                
                $content = file_get_contents($this->filename.'.'.$this->extension);
                
                file_put_contents($this->filename.'.sql.gz', gzencode($content, 9));
                
                // We delete the sql file
                $this->delete();
                
                // Changing file extension to gzip
                $this->extension = 'sql.gz';
                
            break;
        }
    }
    
    
    /**
     * Delete the file
     */
    private function delete()
    {
        if (file_exists($this->filename.'.'.$this->extension))
        {
            unlink($this->filename.'.'.$this->extension);
        }
    }
}