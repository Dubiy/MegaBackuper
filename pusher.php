<?php

echo "begin\n\n";

$chunkSize = 45 * 1024 * 1024 * 1024; //45 Gb

$count = 0;
$totalSize = 0;
$accountId = 0;
$startFile = '';
$skipping = false;
$accounts = [];
$basePath = '';

$db = new SQLite3('pushedFiles.db');
$db->query('CREATE TABLE IF NOT EXISTS "files" (
  "id" integer NOT NULL PRIMARY KEY AUTOINCREMENT,
  "filename" text(1000) NULL,
  "size" integer NULL,
  "accountEmail" text(100) NULL,
  "accountPass" text(100) NULL,
  "time" integer NULL
);');


if (file_exists('config')) {
    $config = json_decode(file_get_contents('config'));
    $totalSize = $config->totalSize;
    $accountId = $config->accountId;
    $startFile = $config->startFile;
    $skipping = true;
}

if (file_exists('accounts')) {
    $rawAccounts = explode("\n", file_get_contents('accounts'));
    foreach ($rawAccounts as $account) {
        $tmp = explode(' ', $account, 2);
        if (count($tmp) == 2) {
            $accounts[] = [
                'email' => trim($tmp[0]),
                'pass' => trim($tmp[1])
            ];
        }
    }
}

function scan($dir)
{
    global $skipping, $startFile, $accountId, $accounts, $basePath;
    $cdir = scandir($dir);
    foreach ($cdir as $key => $value) {
        if (!in_array($value, [".",".."])) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                if (!$skipping) {
                    if (!isset($accounts[$accountId])) {
                        echo "Account #$accountId not exitst!\n\n";
                        return;
                    }
                    $EMAIL = $accounts[$accountId]['email'];
                    $PASS = $accounts[$accountId]['pass'];

                    $relativeDir = str_replace($basePath, '', $dir . DIRECTORY_SEPARATOR . $value);

                    `/usr/bin/megamkdir -u "$EMAIL" -p "$PASS" /Root/$relativeDir`;
                }
                scan($dir . DIRECTORY_SEPARATOR . $value);
            } else {
                if ($skipping) {
                    if ($startFile == $dir . DIRECTORY_SEPARATOR .$value) {
                        $skipping = false;
//                        processFile($dir . DIRECTORY_SEPARATOR .$value);
                    }
                    continue;
                }
                processFile($dir . DIRECTORY_SEPARATOR .$value);
            }
        }
    }
}

function processFile($file)
{
    global $basePath, $count, $totalSize, $chunkSize, $accountId, $accounts, $db;
    $count++;
    echo $file . "\n";

    $filesize = filesize ($file);
    $totalSize += $filesize;


//upload here

    if (!isset($accounts[$accountId])) {
        echo "Account #$accountId not exitst!\n\n";
        return;
    }

    $EMAIL = $accounts[$accountId]['email'];
    $PASS = $accounts[$accountId]['pass'];

    $relativeFilename = str_replace($basePath, '', $file);

    //upload here

    `/usr/bin/megaput -u "$EMAIL" -p "$PASS" --path "/Root/$relativeFilename" "$file"`;


    $db->query('INSERT INTO "files" ("filename", "size", "accountEmail", "accountPass", "time") VALUES ("' . $db->escapeString($file) . '", "' . $filesize . '", "' . $db->escapeString($EMAIL) . '", "' . $db->escapeString($PASS) . '", "' . time() . '");');

    if ($totalSize > $chunkSize) {
        $totalSize = 0;
        //next account
        $accountId++;
    }

    file_put_contents('config', json_encode([
        'totalSize' => $totalSize,
        'accountId' => $accountId,
        'startFile' => $file
    ]));
}

$basePath = getcwd() . DIRECTORY_SEPARATOR;

scan($basePath);

echo "\n\nend\n$count\n\n$totalSize\n";

echo "Acc id: $accountId\n\n";


//TODO: param dest directory
//TODO: scan not uploaded files (not in db)

$db->close();