<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['posts'] = Post::all();

        return response()->json([
            'success' => true,
            'message'=>"All Post Data.",
            'data' => $data
        ],200);
    }

    /**==================================================
     =============== STORE ============================
     ==================================================*/
    public function store(Request $request)
    {
        // Validate the request data
        $validateUser = Validator::make(
            $request->all(),[
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png',
        ]);

        // If validation fails, return error response
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error",
                'errors' => $validateUser->errors()->all(),
            ], 401);
        }

        // Handle the image upload
        $img = $request->file('image'); // Use file() to get the uploaded file
        $extension = $img->getClientOriginalExtension(); // Get file extension
        $imageName = time() . '.' . $extension; // Create a unique image name
        $img->move(public_path('images/uploads'), $imageName); // Move the file to the uploads folder

        // Create the post record
        $post = Post::create([
            'title' => $request->title, // Use $request instead of $required
            'description' => $request->description,
            'image' => $imageName,
        ]);

        // Return success response
        return response()->json([
            'status' => true,
            'message' => "Post Created Successfully",
            'post' => $post,
        ], 200);
    }




    /**==================================================
    ====================  show =======================
    ==================================================*/

    public function show(string $id)
    {
        $data['post']=Post::select(
            'id',
            'title',
            'description',
            'image',
        )->where("id",$id)->get();


        return response()->json([
            'status' => true,
            'message' => "Your singel post",
            'data' => $data,
        ], 200);
    }




    /**==================================================
    ====================  update =======================
    ==================================================*/


    public function update(Request $request, string $id)
    {
        // Validate the request data
        $validateUser = Validator::make(
            $request->all(), [
            'title' => 'required',
            'description' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png',
        ]);

        // If validation fails, return error response
        if ($validateUser->fails()) {
            return response()->json([
                'status' => false,
                'message' => "Validation error",
                'errors' => $validateUser->errors()->all(),
            ], 401);
        }

        // Retrieve the post and check if it exists
        $post = Post::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => "Post not found",
            ], 404);
        }

        // Handle the image update
        $path = public_path('images/uploads'); // Define the path
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($post->image && file_exists($path . '/' . $post->image)) {
                unlink($path . '/' . $post->image);
            }

            // Save the new image
            $img = $request->file('image');
            $extension = $img->getClientOriginalExtension();
            $imageName = time() . '.' . $extension;
            $img->move($path, $imageName); // Move new image to the uploads directory
        } else {
            // If no new image is uploaded, retain the old one
            $imageName = $post->image;
        }

        // Update the post record
        $post->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName,
        ]);

        // Return success response
        return response()->json([
            'status' => true,
            'message' => "Post Updated Successfully",
            'post' => $post,
        ], 200);
    }







    /**==================================================
    ====================  Destroy =======================
    ==================================================*/


    public function destroy(string $id)
    {

        $imagePath = Post::select('image')->where('id',$id)->get();
        $filepath = public_path(). '/uploads'. $imagePath[0]['image'];
        unlink($filepath);


        $post = Post::where('id',$id)->delete();


        return response()->json([
            'status' => true,
            'message' => "Your Post has been Remove",
            'post' => $post,
        ], 200);
    }
}
