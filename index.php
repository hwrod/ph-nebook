<?
$_ = parse_ini_file(dirname(__FILE__) . '/config.ini.php', true);
error_reporting(E_ERROR);
require_once 'api/phonebook_api.php';
$phonebook_api = new PhonebookAPI();

$entries = $phonebook_api->entries;

$mode = $_GET['mode'];
$sort = $_GET['sort'];
$dir = !intval($_GET['dir']);

# Generate entry words for the search function.
$entry_words = [];
array_walk_recursive($entries, function($entry) use (&$entry_words) {
    $entry_words[] = $entry;
});

# Utility HTML generator.
function sort_for($col, $sort, $dir) {
    $col_sm = strtolower(str_replace(' ', '', $col));
    return "
        <a href=\"?sort=$col_sm&dir=".intval($dir)."\" title=\"Sortable\" data-toggle=\"tooltip\" class=\"sort\">
            $col
            <span class=\"glyphicon glyphicon-sort\" data-sort=\"$col_sm\"></span>
        </a>
    ";
}

?>
<html>
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title><?=$_["app"]["name"]?></title>
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet" integrity="sha256-MfvZlkHCEqatNoGiOXveE8FIwMzZg4W85qfrfIFBfYc= sha512-dTfge/zgoMYpP7QbHy4gWMEGsbsdZeCXz7irItjcC3sPUFtf0kuFbDz/ixG7ArTxmDjLXDmezHubeNikyKGVyQ==" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="css/main.css">
    </head>
    <body>

    <nav class="navbar navbar-inverse" style="border-radius: 0">
        <form class="navbar-form navbar-left" role="search">
            <h1 style="color: #efefef; display: inline"><?=$_["app"]["name"]?>
                <? if ($mode) {?>
                <a href="?" class="btn btn-default" style="position: relative; top: -3px; left: 2em">
                    <span class="glyphicon glyphicon-chevron-left"></span> Back
                </a>
                <? } else { ?>
                <a href="?mode=add" class="btn btn-default" style="position: relative; top: -3px; left: 2em">
                    <span class="glyphicon glyphicon-plus-sign"></span> New Entry
                </a>
                <? } ?>
            </h1>
        </form>
        <form class="navbar-form navbar-right" role="search" style="margin-right: 10px">
            <input type="text" class="form-control search typeahead" placeholder="Search">
        </form>
    </nav>

<?

if ($mode) {

 $entry = array_values($phonebook_api->get_entry($_GET["id"]));
 foreach ($entry as $n => $entry) {}

?>

    <div class="row">
        <div class="col-xs-offset-4 col-xs-6">
            <form action="api/index.php" method="POST">
                <input type="hidden" name="do" value="<?=$mode?>">
                <input type="hidden" name="id" value="<?=$entry["id"]?>">
                <fieldset class="form-group">
                    <label>First Name
                        <input type="text" class="form-control" name="phonenumber[firstname]" placeholder="" required="required" value="<?=$entry["firstname"]?>">
                    </label>
                    <label>Last Name
                        <input type="text" class="form-control" name="phonenumber[lastname]" placeholder="" required="required" value="<?=$entry["lastname"]?>">
                    </label>
                </fieldset>
                <fieldset class="form-group">
                    <label>Phone Number
                        <input type="text" class="form-control" name="phonenumber[phonenumber]" placeholder="###-###-####" required="required" value="<?=$entry["phonenumber"]?>">
                    </label>
                </fieldset>
                <fieldset class="form-group">
                    <label>Date of Birth
                        <input type="text" class="form-control" name="phonenumber[dateofbirth]" placeholder="MM/DD/YYYY" required="required" value="<?=$entry["dateofbirth"]?>">
                    </label>
                </fieldset>
                <button type="submit" name="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>

<?  } else { # Not in Add/Edit mode. Display table instead.  ?>

    <table class="table table-striped">
        <tr>
            <th><?=sort_for('First Name', $sort, $dir)?></th>
            <th><?=sort_for('Last Name', $sort, $dir)?></th>
            <th><?=sort_for('Phone Number', $sort, $dir)?></th>
            <th><?=sort_for('Date of Birth', $sort, $dir)?></th>
            <th>Actions</th>
        </tr>
        <?
            if ($sort) {
                uasort($entries, function ($a, $b) use ($sort, $dir) { return $dir ? $a[$sort] < $b[$sort] : $a[$sort] > $b[$sort]; });
            }
           foreach ($entries as $entry) {
        ?>
        <tr class="data">
            <td><?=$entry['firstname']?></td>
            <td><?=$entry['lastname']?></td>
            <td><?=$entry['phonenumber']?></td>
            <td><?=$entry['dateofbirth']?></td>
            <td>
                <a href="?mode=edit&id=<?=$entry['id']?>">
                    <span   class="glyphicon glyphicon-pencil"  title="Edit"   data-toggle="tooltip"></span>
                </a>
                <a href="api?do=delete&id=<?=$entry['id']?>">
                    <span class="glyphicon glyphicon-minus-sign" title="Delete" data-toggle="tooltip"></span>
                </a>
            </td>
        </tr>
        <? } ?>
    </table>
<? } ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/3.10.1/lodash.min.js"></script>
    <script src="https://code.jquery.com/jquery-2.1.4.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js" integrity="sha256-Sk3nkD6mLTMOF0EOpNtsIry+s1CsaqQC1rVLTAy+0yc= sha512-K1qjQ+NcF2TYO/eI3M6v8EiNYZfA95pQumfvcVrTHtwQVDG+aHRqLi/ETn2uB+1JqwYqVG3LIvdm9lj6imS/pQ==" crossorigin="anonymous"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/main.js"></script>
    <script>
        var states = <?=json_encode($entry_words)?>;

        $('.typeahead').typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        },
        {
            name: 'states',
            source: substringMatcher(states)
        });
    </script>

</body>
</html>
