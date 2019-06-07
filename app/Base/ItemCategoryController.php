<?php

namespace App\Http\Controllers;

use Auth;
use App\Http\Controllers\ApplicationController;
use Illuminate\Http\Request;

class ItemCategoryController extends ApplicationController
{

    protected $type;
    protected $class;
    protected $storer;

    private function itens()
    {
        return Auth::user()->categoryOf($this->type);
    }

    private function itensNotRemoved()
    {
        return $this->itens()->notRemoved()->get();
    }

    private function categories()
    {
        return Auth::user()->categories($this->type);
    }

    private function categoriesNotRemovedWithoutFather()
    {
        return $this->categories()->notRemoved()->withoutFather()->get();
    }
	
    public function index()
    {
        $items = $this->itensNotRemoved();
        return $this->indexView($items);
    }
	
    public function indexFromCategory($category)
    {
        $items = $this->itensNotRemoved()->fromCategory($category)->get();
        return $this->indexView($items, $category);
    }
	
    public function indexWithoutCategory()
    {
        $items = $this->itensNotRemoved()->withoutCategory()->get();
        return $this->indexView($items);
    }

    public function create()
    {
        return $this->creaveView();
    }

    public function createFromCategory($category)
    {
        return $this->creaveView($category);
    }

    private function indexView($items, $category = null)
    {
        return view($this->type.".view", [
            "items" => $items,
            "category" => $category,
            "itemCategories" => $this->categoriesNotRemovedWithoutFather()
        ]);
    }
	
    private function creaveView($category = null)
    {
        $class = $this->class;
        return view($this->type.".form", [
            "item" => new $class(),
            "category" => $category,
            "itemCategories" => $this->categoriesNotRemovedWithoutFather()
        ]);
    }
	
    public function store(Request $request)
    {
        $this->storer::run($request);
        return redirect(url($this->type))->with(['message' => __('lang.stored')]);
    }
	
    public function show($item)
    {
        return view($this->type.".confirm", [
            "item" => $item,
        ]);
    }
	
    public function destroy($item)
    {
        $item->update(["soft_delete" => true]);
        return redirect(url($this->type))->with(['message' => __('lang.stored')]);
    }

    public function edit($item)
    {
        return view($this->type.".form",[
            "item" => $item,
            "category" => null,
            "itemCategories" => $this->categoriesNotRemovedWithoutFather()
        ]);
    }

    public function update(Request $request, $item)
    {
        $this->storer::run($request, $item);
        return redirect(url($this->type))->with(['message' => __( 'lang.updated')]);
    }
}