<?php

class PhonebookAPI {

    public $entries;

    private $config;
    private $db;

    public function __construct () {
        # Load main configuration file.
        $this->config = parse_ini_file( dirname(__FILE__).'/../config.ini.php', true);
        $this->db = $this->get_db();
        $this->entries = $this->db["entries"] ? $this->db["entries"] : [];
    }

    public function process_request($request) {
        switch($request["do"]) {
        case "add": 
            $request["phonenumber"]["id"] = uniqid();
            $_SESSION["entries"] []= $request["phonenumber"];
            break;

        case "delete": 
            $entry_obj = $this->get_entry($request["id"]);
            foreach ($entry_obj as $n => $entry) {
                unset($_SESSION["entries"][$n]);
            }
            break;

        case "edit": 
            $entry_obj = $this->get_entry($request["id"]);
            foreach ($entry_obj as $n => $entry) {
                foreach ($entry as $key => $value)
                $_SESSION["entries"][$n][$key] = $value;
            }
            break;
        }
        header("Location: ../");
        exit;
    }

    private function get_db() {
        # MySQL database mockup.
        session_start();
        return $_SESSION;
    }


    public function get_entry($id) {
        return array_filter($_SESSION["entries"], 
            function($entry) use ($id) { 
                return $entry["id"] === $id;
            }
        );
    }
}

?>
