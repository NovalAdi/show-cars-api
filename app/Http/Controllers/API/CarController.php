<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;

class CarController extends Controller
{

    function index()
    {
        $car = Car::all();
        foreach ($car as $key => $value) {
            $value->image = url($value->image);
        }
        return response()->json(
            [
                'status' => true,
                'data' => $car,
            ]
        );
    }

    function getMainCar()
    {
        $car = Car::orderBy('price', 'desc')->first();

        $car->image = url($car->image);

        return response()->json(
            [
                'status' => true,
                'data' => $car,
            ]
        );
    }

    function show($id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'data not found',
                ],
                404
            );
        }

        return response()->json(
            [
                'status' => true,
                'data' => $car,
            ]
        );
    }

    function create(Request $request)
    {

        $input = $request->all();
        $rules = [
            'name' => 'required',
            'brand' => 'required',
            'price' => 'required',
            'image' => 'required|file|mimes:png,jpg,jpeg'
        ];

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $validator->errors()
                ],
                400
            );
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = 'uploads/';
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '.' . $extension;
            $img = Image::make($file->getRealPath());
            $img->save($path . $fileName);
            $input['image'] = 'uploads/'. $fileName;
        }
        $car = Car::create($input);

        return response()->json(
            [
                'status' => true,
                'data' => $car
            ]
        );
    }

    function update(Request $request, $id)
    {
        $car = Car::find($id);
        $input = $request->all();

        if (!$car) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'data not found',
                ],
                404
            );
        }

        if ($request->file('image')) {
            $file = substr($car->image, 22);
            File::delete($file);
            $fileName = $request->file('image')->getClientOriginalName();
            $request->file('image')->storeAs('uploads', $fileName);
            $input['image'] = url('storage/uploads/' . $fileName);
        }

        $car->update($input);
        return response()->json([
            'status' => true,
            'message' => 'data successfully updated',
        ]);
    }

    public function delete($id)
    {
        $car = Car::find($id);
        if (!$car) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'car not found'
                ],
                400
            );
        }

        $file = substr($car->image, 22);
        File::delete($file);

        $car->delete($id);
        return response()->json(
            [
                'status' => true,
                'message' => 'data succcessfully deleted'
            ]
        );
    }
}
