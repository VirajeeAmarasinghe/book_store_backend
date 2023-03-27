<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Book;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\BookResource;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $books = Auth::user()->books;

        return response()->json(['books' => BookResource::collection($books), 'message' => 'Successful'], 200);

    }

    public function searchBooks(Request $request){ 
 
        $books = [];

        if($request->title !== "" && $request->title !== null && $request->author !== "" && $request->author !== null){

            $author = $request->author; 

            $books = Book::where('user_id', function($query) use ($author){

                $query->select('id')->from(with(new User)->getTable())->where('name','like','%'.$author.'%');
    
            })->where('name','like','%'.$request->title.'%')->get();            
            

        } elseif($request->title !== "" && $request->title !== null){  

            $books = Book::where('name','like','%'.$request->title.'%')->get();               

        }elseif($request->author !== "" && $request->author !== null){

            $author = $request->author; 

            $books = Book::where('user_id', function($query) use ($author){

                $query->select('id')->from(with(new User)->getTable())->where('name','like','%'.$author.'%');
    
            })->get(); 

        }

        return response()->json(['books' => $books]);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all(); 

        $validator = Validator::make($data, [
            'name' => 'required|max:50',
            'isbn' => 'required|max:50' ,
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',           
        ]);

        if($validator->fails()){

            return response()->json(['error' => $validator->errors(), 'Validation Error']);

        }

        $coverImage = time().'.'.$request->cover_image->extension();  

        $request->cover_image->move(public_path('images'), $coverImage);

        $data['cover_image'] = $coverImage;

        $data['user_id'] = Auth::id();

        $book = Book::create($data);

        return response()->json(['book' => new BookResource($book), 'message' => 'Success'], 200);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        
        return response()->json(['book' => new BookResource($book), 'message' => 'Success'], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        
        $book->update($request->all());

        return response()->json(['book' => new BookResource($book), 'message' => 'Success'], 200);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return response()->json(['message' => 'Book deleted']);
    }
}
