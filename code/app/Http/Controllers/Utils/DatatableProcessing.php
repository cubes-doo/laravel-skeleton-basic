<?php

namespace App\Http\Controllers\Utils;

trait DatatableProcessing
{
	protected function processDatatable(
		$request,
		$columns,
		$query,
		$generalSearchScope,
		$orderingScope,
		$filteringScope,
		$forceEntity
	)
	{
		$datatableParameters = $request->all();

        $limit = 10;
        $offset = 0;
        $draw = 1;
        
        if (isset($datatableParameters['draw'])) {

            $draw = $datatableParameters['draw'];

            if (isset($datatableParameters['length'])) {

                //limit rows per page
                $limit = $datatableParameters['length'];

                if (isset($datatableParameters['start'])) {
                    $offset = $datatableParameters['start'];
                }
            }

            if (
                isset($datatableParameters['order']) && is_array($datatableParameters['order'])
            ) {

                foreach ($datatableParameters['order'] as $datatableOrder) {
                    $columnIndex    = $datatableOrder['column'];
                    $orderDirection = strtoupper($datatableOrder['dir']) == 'DESC' ? 'desc' : 'asc';
					
					
					
                    if (isset($columns[$columnIndex]) && !in_array($columns[$columnIndex], ['photo', 'actions'])) {
                        $query->orderBy($columns[$columnIndex], $orderDirection);
                    }
                }
            }

            if (
                isset($datatableParameters['search']) && is_array($datatableParameters['search']) && isset($datatableParameters['search']['value'])
            ) {
                $generalSearch = $datatableParameters['search']['value'];
                
				$generalSearchScope
                $query->where(function($query) use ($generalSearch) {
                    $query->orWhere('email', 'LIKE', '%' .  $generalSearch. '%')
                        ->orWhere('first_name', 'LIKE', '%' . $generalSearch . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $generalSearch . '%');
                });
            }


            if (
                isset($datatableParameters['columns']) && is_array($datatableParameters['columns'])
            ) {
                foreach ($datatableParameters['columns'] as $columnIndex => $column) {

                    if (!isset($columns[$columnIndex])) {
                        continue;
                    }

                    if (!isset($column['search']) || !is_array($column['search'])) {
                        continue;
                    }

                    if (!isset($column['search']['value'])) {
                        continue;
                    }

                    $searchValue = $column['search']['value'];
                    $columnName  = $columns[$columnIndex];

                    switch ($columnName) {
                        case 'status':
                        case 'role':
                            $query->where($columnName, $searchValue);
                            break;
                        case 'email':
                        case 'first_name':
                        case 'last_name':
                            $query->where($columnName, 'LIKE', '%' . $searchValue . '%');
                            break;
                    }
                }
            }
        }
        
        $page = floor(($offset + $limit) / $limit);
        if ($page <= 0) {
            $page = 1;
        }
        
        $entities = $query->paginate($limit, ['*'], 'page', $page);
		
		return $query;
	}
	
	
}