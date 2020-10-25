<?php

/***************************************************************************
 *  mysql4.php
 * -------------------
 * begin : Saturday, Feb 13, 2001
 * copyright : (C) 2001 The phpBB Group
 * email : supportphpbb.com
 *
 * $Id: mysql4.php,v 1.5 2002/04/02 21:13:47 the_systech Exp $
 ***************************************************************************/

/***************************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 ***************************************************************************/
if (!defined('SQL_LAYER')) {
    define('SQL_LAYER', 'mysql4');

    class sql_db
    {
        public $db_connect_id;

        public $query_result;

        public $row = [];

        public $rowset = [];

        public $num_queries = 0;

        public $in_transaction = 0;

        // Constructor

        public function __construct($sqlserver, $sqluser, $sqlpassword, $database, $persistency = 0)
        {
            $this->persistency = $persistency;

            $this->user = $sqluser;

            $this->password = $sqlpassword;

            $this->server = $sqlserver;

            $this->dbname = $database;

            $this->db_connect_id = ($this->persistency) ? mysql_pconnect($this->server, $this->user, $this->password) : mysql_connect($this->server, $this->user, $this->password);

            if ($this->db_connect_id) {
                if ('' != $database) {
                    $this->dbname = $database;

                    $dbselect = mysqli_select_db($GLOBALS['xoopsDB']->conn, $this->dbname);

                    if (!$dbselect) {
                        $GLOBALS['xoopsDB']->close($this->db_connect_id);

                        $this->db_connect_id = $dbselect;
                    }
                }

                return $this->db_connect_id;
            }

            return false;
        }

        // Other base methods

        public function sql_close()
        {
            if ($this->db_connect_id) {
                // Commit any remaining transactions

                if ($this->in_transaction) {
                    $GLOBALS['xoopsDB']->queryF('COMMIT', $this->db_connect_id);
                }

                return $GLOBALS['xoopsDB']->close($this->db_connect_id);
            }

            return false;
        }

        // Base query method

        public function sql_query($query = '', $transaction = false)
        {
            // Remove any pre-existing queries

            unset($this->query_result);

            if ('' != $query) {
                $this->num_queries++;

                if (BEGIN_TRANSACTION == $transaction && !$this->in_transaction) {
                    $result = $GLOBALS['xoopsDB']->queryF('BEGIN', $this->db_connect_id);

                    if (!$result) {
                        return false;
                    }

                    $this->in_transaction = true;
                }

                $this->query_result = $GLOBALS['xoopsDB']->queryF($query, $this->db_connect_id);
            } else {
                if (END_TRANSACTION == $transaction && $this->in_transaction) {
                    $result = $GLOBALS['xoopsDB']->queryF('COMMIT', $this->db_connect_id);
                }
            }

            if ($this->query_result) {
                unset($this->row[$this->query_result]);

                unset($this->rowset[$this->query_result]);

                if (END_TRANSACTION == $transaction && $this->in_transaction) {
                    $this->in_transaction = false;

                    if (!$GLOBALS['xoopsDB']->queryF('COMMIT', $this->db_connect_id)) {
                        $GLOBALS['xoopsDB']->queryF('ROLLBACK', $this->db_connect_id);

                        return false;
                    }
                }

                return $this->query_result;
            }

            if ($this->in_transaction) {
                $GLOBALS['xoopsDB']->queryF('ROLLBACK', $this->db_connect_id);

                $this->in_transaction = false;
            }

            return false;
        }

        // Other query methods

        public function sql_numrows($query_id = 0)
        {
            if (!$query_id) {
                $query_id = $this->query_result;
            }

            return ($query_id) ? $GLOBALS['xoopsDB']->getRowsNum($query_id) : false;
        }

        public function sql_affectedrows()
        {
            return ($this->db_connect_id) ? $GLOBALS['xoopsDB']->getAffectedRows($this->db_connect_id) : false;
        }

        public function sql_numfields($query_id = 0)
        {
            if (!$query_id) {
                $query_id = $this->query_result;
            }

            return ($query_id) ? mysqli_num_fields($query_id) : false;
        }

        public function sql_fieldname($offset, $query_id = 0)
        {
            if (!$query_id) {
                $query_id = $this->query_result;
            }

            return ($query_id) ? $GLOBALS['xoopsDB']->getFieldName($query_id, $offset) : false;
        }

        public function sql_fieldtype($offset, $query_id = 0)
        {
            if (!$query_id) {
                $query_id = $this->query_result;
            }

            return ($query_id) ? mysql_field_type($query_id, $offset) : false;
        }

        public function sql_fetchrow($query_id = 0)
        {
            if (!$query_id) {
                $query_id = $this->query_result;
            }

            if ($query_id) {
                $this->row[$query_id] = $GLOBALS['xoopsDB']->fetchBoth($query_id, MYSQL_ASSOC);

                return $this->row[$query_id];
            }

            return false;
        }

        public function sql_fetchrowset($query_id = 0)
        {
            if (!$query_id) {
                $query_id = $this->query_result;
            }

            if ($query_id) {
                unset($this->rowset[$query_id]);

                unset($this->row[$query_id]);

                while (false !== ($this->rowset[$query_id] = $GLOBALS['xoopsDB']->fetchBoth($query_id, MYSQL_ASSOC))) {
                    $result[] = $this->rowset[$query_id];
                }

                return $result;
            }

            return false;
        }

        public function sql_fetchfield($field, $rownum = -1, $query_id = 0)
        {
            if (!$query_id) {
                $query_id = $this->query_result;
            }

            if ($query_id) {
                if ($rownum > -1) {
                    $result = mysql_result($query_id, $rownum, $field);
                } else {
                    if (empty($this->row[$query_id]) && empty($this->rowset[$query_id])) {
                        if ($this->sql_fetchrow()) {
                            $result = $this->row[$query_id][$field];
                        }
                    } else {
                        if ($this->rowset[$query_id]) {
                            $result = $this->rowset[$query_id][$field];
                        } elseif ($this->row[$query_id]) {
                            $result = $this->row[$query_id][$field];
                        }
                    }
                }

                return $result;
            }

            return false;
        }

        public function sql_rowseek($rownum, $query_id = 0)
        {
            if (!$query_id) {
                $query_id = $this->query_result;
            }

            return ($query_id) ? mysql_data_seek($query_id, $rownum) : false;
        }

        public function sql_nextid()
        {
            return ($this->db_connect_id) ? $GLOBALS['xoopsDB']->getInsertId($this->db_connect_id) : false;
        }

        public function sql_freeresult($query_id = 0)
        {
            if (!$query_id) {
                $query_id = $this->query_result;
            }

            if ($query_id) {
                unset($this->row[$query_id]);

                unset($this->rowset[$query_id]);

                $GLOBALS['xoopsDB']->freeRecordSet($query_id);

                return true;
            }

            return false;
        }

        public function sql_error()
        {
            $result['message'] = $GLOBALS['xoopsDB']->error($this->db_connect_id);

            $result['code'] = $GLOBALS['xoopsDB']->errno($this->db_connect_id);

            return $result;
        }
    } // class sql_db
} // if ... define
