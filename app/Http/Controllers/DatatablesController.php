<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DatatablePrimary as Entity;
use App\Models\DatatableChild as Child;
use App\Models\DatatableParent as ParentEntity;
use Illuminate\Support\Facades\DB;

class DatatablesController extends Controller
{
    
//    protected $entityTable = (new Entity())->getTable();
    
    public function primaryShow(){
        return view('datatables.primary');
    }
    
    public function parentShow(){
        return view('datatables.parent');
    }
    
    public function childShow(){
        return view('datatables.child');
    }
    
    public function childrenShow(){
        return view('datatables.children');
    }
    
    /**
     *  Filters and orders datatable.
     *  Data is fetched from single (MAIN) table without relations.
     *  Columns don't need to be listed. You can add or edit columns. 
     *  The js object of datatable tells you which columns are needed.  
     * 
     * @return void
     */
    public function primary()
    {
        return 
            datatables(Entity::query())
                /*
                 *  Datatable by default filter data with global search,
                 *  if needed in filter method you can define filter options
                 */
                ->filter(function ($query) {
                    /*
                     * Check if request has global search
                     */
                    if (request()->has('search')) {
                        $query
                            ->where(function($q){
                                /*
                                 *  Define columns for filtering.
                                 */
                                $q
                                    ->orWhere('title', 'like', '%' . request()['search']['value'] . '%')
                                ;
                            })
                        ;
                    }
                })
                ->editColumn('title', '{{str_cut($title, 20)}}')
                ->addColumn('actions', function ($entity) {
                    return view('datatables.partials.table.actions', compact('entity'));
                })
                ->rawColumns(['active', 'actions'])
                ->setRowAttr([
                    'data-id' => function ($entity) {
                        return $entity->id;
                    },
                ])
                ->make(true);
    }
    
    /**
     * Filters and orders datatable.
     * Data is fetched from main table and her related parent table
     * Columns that correspond to columns from parent table must be listed, 
     * defined in filters and orders, also parent table column names
     * must match with js object column names. 
     * @return void
     */
    public function withParent()
    {
        /* 
         * Define entity and parent table names
         */
        $entityTable = (new Entity())->getTable();
        $parentTable = (new ParentEntity())->getTable();
        
        /*
         * Define which column should be selected in sql query.
         * Parent coulmns should use aliases  
         */
        $select = [
            $entityTable . '.*',
            $parentTable . '.title as parent'
        ];
        /**
         * Building query by selecting and using joins
         */
        $query = Entity::select($select)->leftJoin($parentTable, $parentTable . '.id', '=', $entityTable . '.parent_id');
        
        return 
            datatables($query)
                ->filter(function ($query) use ($entityTable,$parentTable){
                    if (request()->has('search')) {
                        $query
                            /*
                             * Use table variables for easier and faster code reuse 
                             */
                            ->where(function($q) use ($entityTable,$parentTable){
                                $q
                                    ->orWhere($entityTable . '.title', 'like', '%' . request()['search']['value'] . '%')
                                    ->orWhere($parentTable . '.title', 'like', '%' . request()['search']['value'] . '%')
                                ;
                            })
                        ;
                    }
                })
                /*
                 * Adding parent columns witn names that correspond to aliases or names in select
                 */
                ->addColumn('parent', function($entity){
                    return ($entity->parent) ? $entity->parent : 'N/A';
                })
                ->editColumn('title', '{{str_cut($title, 20)}}')
                ->addColumn('actions', function ($entity) {
                    return view('datatables.partials.table.actions', compact('entity'));
                })
                ->rawColumns(['actions'])
                ->setRowAttr([
                    'data-id' => function ($entity) {
                        return $entity->id;
                    },
                ])
                /*
                 * Ordering data by entity columns is implemented by default,
                 * but ordering data by related table columns must be defined.
                 * First attribute defines column name, 
                 * Second attribute defines one or more ordering instrictions in format:
                 * {column} {$1}, {column} {$1}, ... 
                 * $1 = variable that holds direction  
                 */
                ->orderColumn('parent', 'parent $1, ' . $entityTable . '.title $1')     
                ->make(true);
    }
    
    /**
     * Same as previous method (AKA withParent) but with different relationship 
     * @return mixed
     */
    public function withChild()
    {
        $entityTable = (new Entity())->getTable();
        $childTable = (new Child())->getTable();
        
        $select = [
            $entityTable . '.*',
            $childTable . '.title as child'
        ];
        $query = Entity::select($select)->leftJoin($childTable, $childTable . '.parent_id', '=', $entityTable . '.id');
        
        return 
            datatables($query)
                ->filter(function ($query) use ($entityTable,$childTable){
                    if (request()->has('search')) {
                        $query
                            ->where(function($q) use ($entityTable,$childTable){
                                $q
                                    ->orWhere($entityTable . '.title', 'like', '%' . request()['search']['value'] . '%')
                                    ->orWhere($childTable . '.title', 'like', '%' . request()['search']['value'] . '%')
                                ;
                            })
                        ;
                    }
                })
                ->addColumn('child', function($entity){
                    return ($entity->child) ? $entity->child : 'N/A';
                })
                ->editColumn('title', '{{str_cut($title, 20)}}')
                ->addColumn('actions', function ($entity) {
                    return view('datatables.partials.table.actions', compact('entity'));
                })
                ->rawColumns(['actions'])
                ->setRowAttr([
                    'data-id' => function ($entity) {
                        return $entity->id;
                    },
                ])
                ->orderColumn('child', 'child $1,' . $entityTable . '.title $1') // This solution is not best but suitable for most cases
                ->make(true);
    }
    
    public function withChildren()
    {
        $entityTable = (new Entity())->getTable();
        $childrenTable = (new Child())->getTable();
        /*
         * Children table alias, must be defined 
         */
        $childrenTableAlias = 'children';
        $select = [
            $entityTable . '.title',
            /*
             * Aggregate Count column
             */
            'COALESCE('. $childrenTableAlias .'.counter, 0) as children'
        ];
        
        $query = 
            Entity::
                select(DB::raw(implode(',', $select)))
                /*
                 * Joining with children table
                 */
                ->leftJoin(
                    DB::raw(
                            /*
                             * Aggregate count children relationship
                             */
                            '( select parent_id, count(id) as counter from ' . $childrenTable .' group by parent_id ) as ' . $childrenTableAlias ), 
                            $childrenTableAlias . '.parent_id', '=', $entityTable . '.id')

            ;
        return 
            datatables($query)
                ->filter(function ($query) use ($entityTable, $childrenTableAlias) {
                    if (request()->has('search') && !is_null(request()['search']['value'])) {
                            $query
                                ->where(function($q) use ($entityTable, $childrenTableAlias){
                                    
                                    $q
                                        ->orWhere($entityTable . '.title', 'like', "%" . request()['search']['value'] . "%")
                                            
                                    ;
                                    /*
                                     * If global search value is 0, filter must check for parents without children
                                     */
                                    if(request()['search']['value'] == 0){
                                        $q->orWhereNull( $childrenTableAlias . '.counter');
                                    } else {
                                        $q->orWhere($childrenTableAlias .'.counter', 'like', "%" . request()['search']['value'] . "%");
                                    }
                                })

                            ;
                    }
                })
                ->editColumn('title', '{{str_cut($title, 20)}}')
                ->addColumn('children', function ($entity){
                    return $entity->children;
                })
                ->addColumn('actions', function ($entity) {
                    return view('datatables.partials.table.actions', compact('entity'));
                })
                ->rawColumns(['actions'])
                ->setRowAttr([
                    'data-id' => function($entity) {
                        return $entity->id;
                    }
                ])
                ->orderColumn('children', 'children $1, dt_primary.title $1')
                ->make(true)
        ;
    }
}
