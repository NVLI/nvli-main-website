WHAT TO PLACE IN THIS DIRECTORY?
--------------------------------
Placing downloaded and custom modules in this directory separates downloaded and
custom modules from Drupal core's modules. This allows Drupal core to be updated
without overwriting these files.

Modules to be placed in module/contrib folder
 search_api
 search_api_solr
 custom_solr_search

Modules to be placed in module/custom
 nvli_annotation_services
 nvli_resource
 custom_solr_annotation
 
These are the basic module we will need to be installed in Drupal 8 instance.

Apart from drupal 8 modules we need to add few files in solr directory 
 add_annotation_solr.php

 