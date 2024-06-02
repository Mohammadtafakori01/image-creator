<?php
class CommandHandler {
    private $db;

    public function __construct($dbPath) {
        $this->db = new SQLite3($dbPath);
    }

    public function __destruct() {
        $this->db->close();
    }

    public function readAndUpdateCommand() {
        // Select one record where used is 0
        $result = $this->db->querySingle('SELECT * FROM commands WHERE used = 0 LIMIT 1', true);

        if ($result) {
            // Update the used status to 1
            $stmt = $this->db->prepare('UPDATE commands SET used = 1 WHERE id = :id');
            $stmt->bindValue(':id', $result['id'], SQLITE3_INTEGER);
            $stmt->execute();
            return $result;
        } else {
            echo "No unused commands found.\n";
        }
    }
}
