<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\DatatablePrimary as Entity;
use App\Models\DatatableChild as Child;
use App\Models\DatatableParent as ParentEntity;

class DatatablesController extends Controller
{
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
    
    
    public function primary()
    {
        return 
            datatables(Entity::query())
                ->filter(function ($query) {
                    if (request()->has('search')) {
                        $query->where('title', 'like', '%' . request()['search']['value'] . '%');
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
    
    public function withParent()
    {
        return 
            datatables(Entity::with('dtParent'))
                ->filter(function ($query) {
                    if (request()->has('search')) {
                        $query
                            ->where(function($q){
                                $q
                                    ->orWhere('title', 'like', '%' . request()['search']['value'] . '%')
                                    ->orWhereHas('dtParent', function($que){
                                        $que->where('title', 'like', '%' . request()['search']['value'] . '%');
                                    })
                                ;
                            })
                        ;
                    }
                })
                ->addColumn('parent', function($entity){
                    return ($entity->dtParent) ? $entity->dtParent->title : 'N/A';
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
                ->orderColumn('parent', 'parent_id $1') // This solution is not best but suitable for most cases ordering by parent
                ->make(true);
    }
    public function withChild()
    {
        $entityTable = (new Entity())->getTable();
        $childTable = (new Child())->getTable();
        
        $select = [
            $entityTable . '.*',
            $childTable . '.title'
        ];
        $query = Entity::select($select)->leftJoin($childTable, $childTable . '.parent_id', '=', $entityTable . '.id');
        dd($select, $query->toSql());
        return 
            datatables($query)
//                ->filter(function ($query) {
//                    if (request()->has('search')) {
//                        $query
//                            ->where(function($q){
//                                $q
//                                    ->orWhere('title', 'like', '%' . request()['search']['value'] . '%')
//                                    ->orWhereHas('dtChild', function($que){
//                                        $que->where('title', 'like', '%' . request()['search']['value'] . '%');
//                                    })
//                                ;
//                            })
//                        ;
//                    }
//                })
                ->addColumn('child', function($entity){
                    return ($entity->dtChild) ? $entity->dtChild->title : 'N/A';
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
//                ->orderColumn('child', 'child $1') // This solution is not best but suitable for most cases
                ->make(true);
    }
    public function withChildren()
    {
        return 
            datatables(Entity::query())
                ->filter(function ($query) {
                    if (request()->has('search')) {
                        $query->where('title', 'like', '%' . request()['search']['value'] . '%');
                    }
                })
                ->editColumn('active', function ($entity) {
                    return view('entities.partials.table.active', compact('entity'));
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
}
