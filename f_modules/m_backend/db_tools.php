<?php
/*******************************************************************************************************************
| Database Tools (Admin)
| - Export entire database as .sql.gz
| - Import .sql or .sql.gz (use with caution)
*******************************************************************************************************************/

define('_ISVALID', true);
include_once '../../f_core/config.core.php';

// Require backend access
if (!VSession::isLoggedIn() || !VLogin::checkBackendAccess()) {
    header('Location: /error');
    exit;
}

// Convenience
function dbtools_output_gzip($filename, $content_cb) {
    header('Content-Type: application/gzip');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    // Stream gzip
    $gz = fopen('php://temp', 'wb+');
    $buffer = '';
    $content_cb(function($chunk) use (&$buffer) { $buffer .= $chunk; });
    echo gzencode($buffer, 6);
}

function dbtools_escape_value($db, $val) {
    if ($val === null) return 'NULL';
    return $db->qStr($val); // returns quoted string
}

function dbtools_export_sql() {
    global $db;
    @set_time_limit(0);
    $date = date('Ymd_His');
    $fname = 'easystream_backup_' . $date . '.sql.gz';

    dbtools_output_gzip($fname, function($write) use ($db) {
        $write("-- EasyStream SQL Backup\n");
        $write("-- Generated: " . date('c') . "\n\n");
        $write("SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n");

        $tables = $db->GetCol('SHOW TABLES');
        if (!is_array($tables)) $tables = [];
        foreach ($tables as $t) {
            $write("--\n-- Table structure for `{$t}`\n--\n\n");
            $write("DROP TABLE IF EXISTS `{$t}`;\n");
            $crt = $db->GetRow("SHOW CREATE TABLE `{$t}`");
            if ($crt && isset($crt['Create Table'])) {
                $write($crt['Create Table'] . ";\n\n");
            }

            $rs = $db->Execute("SELECT * FROM `{$t}`");
            if ($rs && !$rs->EOF) {
                $write("--\n-- Dumping data for table `{$t}`\n--\n\n");
                $cols = array_keys($rs->fields);
                $colList = '`' . implode('`,`', $cols) . '`';
                $batch = [];
                $batchSize = 100; // rows per INSERT
                while (!$rs->EOF) {
                    $row = [];
                    foreach ($cols as $c) {
                        $row[] = dbtools_escape_value($db, $rs->fields[$c]);
                    }
                    $batch[] = '(' . implode(',', $row) . ')';
                    if (count($batch) >= $batchSize) {
                        $write("INSERT INTO `{$t}` ({$colList}) VALUES\n" . implode(",\n", $batch) . ";\n");
                        $batch = [];
                    }
                    $rs->MoveNext();
                }
                if (!empty($batch)) {
                    $write("INSERT INTO `{$t}` ({$colList}) VALUES\n" . implode(",\n", $batch) . ";\n");
                }
                $write("\n");
            }
        }
        $write("SET FOREIGN_KEY_CHECKS=1;\n");
    });
    exit;
}

function dbtools_import_sql($path, $isGz) {
    global $db;
    @set_time_limit(0);
    $handle = $isGz ? gzopen($path, 'rb') : fopen($path, 'rb');
    if (!$handle) return ['ok' => false, 'msg' => 'Unable to open uploaded file'];

    $buffer = '';
    $count = 0; $err = 0;
    $readFn = function() use ($handle, $isGz) { return $isGz ? gzgets($handle, 65536) : fgets($handle, 65536); };
    while (($line = $readFn()) !== false) {
        $trim = trim($line);
        if ($trim === '' || strpos($trim, '--') === 0) continue; // skip comments
        $buffer .= $line;
        if (substr(rtrim($line), -1) === ';') {
            $sql = trim($buffer);
            $buffer = '';
            try {
                $db->Execute($sql);
                $count++;
            } catch (Exception $e) {
                $err++;
            }
        }
    }
    if ($isGz) gzclose($handle); else fclose($handle);
    return ['ok' => $err === 0, 'executed' => $count, 'errors' => $err];
}

// Actions
$action = VSecurity::getParam('action', 'alpha', '');
if ($action === 'export') {
    dbtools_export_sql();
}

$msg = null; $err = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && VSecurity::validateCSRFFromPost('db_tools_import')) {
    if (!isset($_FILES['sqlfile']) || $_FILES['sqlfile']['error'] !== UPLOAD_ERR_OK) {
        $err = 'No file uploaded or upload error.';
    } else {
        $fname = $_FILES['sqlfile']['name'];
        $tmp   = $_FILES['sqlfile']['tmp_name'];
        $size  = (int) $_FILES['sqlfile']['size'];
        if ($size <= 0 || $size > 100 * 1024 * 1024) { // 100MB limit
            $err = 'Invalid file size.';
        } else {
            $isGz = preg_match('/\.gz$/i', $fname) === 1;
            $res = dbtools_import_sql($tmp, $isGz);
            if ($res['ok']) {
                $msg = 'Import completed. Executed ' . (int)$res['executed'] . ' statements.';
            } else {
                $err = 'Import finished with ' . (int)$res['errors'] . ' errors, executed ' . (int)$res['executed'] . ' statements.';
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>Database Tools - EasyStream Admin</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .card { border:1px solid #ddd; padding:20px; border-radius:6px; margin-bottom:20px; }
    .btn { padding: 10px 16px; background:#1976d2; color:#fff; border:none; border-radius:4px; cursor:pointer; }
    .btn:hover { background:#1565c0; }
    .btn.secondary { background:#6c757d; }
    .alert { padding: 12px; border-radius: 4px; margin-bottom: 15px; }
    .alert.success { background:#e8f5e9; color:#1b5e20; border:1px solid #c8e6c9; }
    .alert.error { background:#ffebee; color:#b71c1c; border:1px solid #ffcdd2; }
  </style>
  <meta charset="utf-8" />
</head>
<body>
  <h1>Database Tools</h1>

  <?php if ($msg): ?><div class="alert success"><?php echo secure_output($msg); ?></div><?php endif; ?>
  <?php if ($err): ?><div class="alert error"><?php echo secure_output($err); ?></div><?php endif; ?>

  <div class="card">
    <h2>Export Database</h2>
    <p>Download a gzip-compressed SQL dump of the entire database. Includes schema and data.</p>
    <p><strong>Note:</strong> For large datasets, export may take a while.</p>
    <a class="btn" href="?action=export">Download .sql.gz</a>
  </div>

  <div class="card">
    <h2>Import Database</h2>
    <p>Upload a .sql or .sql.gz dump. Only use dumps generated by this tool for best compatibility.</p>
    <form method="post" enctype="multipart/form-data">
      <?php echo csrf_field('db_tools_import'); ?>
      <input type="file" name="sqlfile" accept=".sql,.gz" required />
      <button class="btn secondary" type="submit">Import</button>
    </form>
  </div>

  <p><em>Tip:</em> Consider placing site in maintenance mode before import. Back up first.</p>
</body>
</html>

