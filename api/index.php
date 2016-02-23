<? 

# If we have a request to this URI, process it.
if (isset($_REQUEST["do"])) {
    require_once 'phonebook_api.php';
    $phonebook_api = new PhonebookAPI();
    $phonebook_api->process_request($_REQUEST);
}

?>
