<?php

namespace CPM\Category\Controllers;

use WP_REST_Request;
use CPM\Category\Models\Category;
use League\Fractal;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use CPM\Transformer_Manager;
use CPM\Category\Transformer\Category_Transformer;

class Category_Controller {
    use Transformer_Manager;

    public function index( WP_REST_Request $request ) {
        $categories = Category::paginate();

        $category_collection = $categories->getCollection();

        $resource = new Collection( $category_collection, new Category_Transformer );
        $resource->setPaginator( new IlluminatePaginatorAdapter( $categories ) );

        return $this->get_response( $resource );
    }

    public function show( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $category = Category::findOrFail( $id );

        $resource = new Item( $category, new Category_Transformer );

        return $this->get_response( $resource );
    }

    public function store( WP_REST_Request $request ) {
        $data = [
            'title' => $request->get_param( 'title' ),
            'description' => $request->get_param( 'description' ),
            'categorible_type' => $request->get_param( 'categorible_type' )
        ];
        $data = array_filter( $data );

        $category = Category::create( $data );

        $resource = new Item( $category, new Category_Transformer );

        return $this->get_response( $resource );
    }

    public function update( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );

        $data = [
            'title' => $request->get_param( 'title' ),
            'description' => $request->get_param( 'description' ),
            'categorible_type' => $request->get_param( 'categorible_type' )
        ];

        $category = Category::findOrFail( $id );
        $data = array_filter( $data );

        $category->update( $data );

        $resource = new Item( $category, new Category_Transformer );

        return $this->get_response( $resource );
    }

    public function destroy( WP_REST_Request $request ) {
        $id = $request->get_param( 'id' );
        $category = Category::findOrFail( $id );
        $category->delete();
    }
}