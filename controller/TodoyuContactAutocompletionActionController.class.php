<?php

class TodoyuContactAutocompletionActionController extends TodoyuActionController {
	
	public function userAction(array $params) {
		$sword	= trim($params['sword']);
		$config	= array();
		
		$results= TodoyuUserFilterDataSource::autocompleteUsers($sword, $config);
		
		return TodoyuRenderer::renderAutocompleteList($results);
	}
	
	public function companyAction(array $params) {
		$sword	= trim($params['sword']);
		$config	= array();
		
		$results= TodoyuUserFilterDataSource::autocompleteCustomers($sword, $config);
		
		return TodoyuRenderer::renderAutocompleteList($results);
	}
	
	
	public function regionAction(array $params) {
		$sword	= trim($params['sword']);
		$config	= array();
		
		$results= TodoyuDatasource::autocompleteRegions($sword, $config);

		return TodoyuRenderer::renderAutocompleteList($results);
	}
		
}

?>