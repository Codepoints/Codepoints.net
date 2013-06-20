<!DOCTYPE html>
<style>
body {
  margin: 0;
  font: 75%/1.25 Droid Sans,Arial,sans-serif;
}
table {
  border-collapse: collapse;
}
th, td {
  border: 1px solid #777;
  padding: 3px 5px;
}
.long {
  color: red;
}
.exists {
  background: #ddd;
}
</style>
<title>UCotD</title>
<table>
<thead>
  <tr>
    <th>Date</th>
    <th>Tweet</th>
    <th>Length</th>
  </tr>
</thead>
<tbody>
<?php

/**
 * Get the CotD of today
 */
function get_ucotd() {
    $db = new PDO('sqlite:'.realpath(__DIR__.'/../ucd.sqlite'));
    $stm = $db->prepare('SELECT cp, na, comment, date
                        FROM dailycp
                        LEFT JOIN codepoints USING ( cp )
                        ORDER BY date');
    $stm->execute();
    return $stm->fetchAll(PDO::FETCH_ASSOC);
}
function unichr($cp) {
    if (hexdec($cp) < 20) {
        $cp = "24" . substr($cp, 2);
    }
    return mb_convert_encoding("&#x$cp;", "UTF-8", "HTML-ENTITIES");
}

$cps = get_ucotd();
$cache = array();
foreach ($cps as $status) {
    $s = sprintf("Daily #Unicode: %s U+%04X %s, %s #codepoints",
        unichr(dechex((int)$status['cp'])),
        $status['cp'],
        $status['na'],
        $status['comment']);
    $len = mb_strlen($s, 'UTF-8');
    $class = '';

    if ($len < 111) {
        $s .= sprintf(' http://codepoints.net/U+%04X', $status['cp']);
        $len = mb_strlen($s, 'UTF-8');
    }
    if ($len > 140) {
        $class .= ' long';
    }
    if (in_array($status['cp'], $cache)) {
        $class .= ' exists';
    }
    printf('<tr class="%s"><td>%s</td><td>%s</td><td>%s</td></tr>', $class,
        $status['date'], $s, mb_strlen($s, 'UTF-8'));
    $cache[] = $status['cp'];
}
?>
</tbody>
</table>
