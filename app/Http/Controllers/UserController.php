<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginAuth(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = $request->only(['email', 'password']);
        if(Auth::attempt($user)){
            return redirect()->route('home.page');
        }else{
            return redirect()->back()->with('failed', 'Proses login gagal! silahkan coba kembali menggunakan data yang benar!');
        }
    }

    public function logout(){
        Auth::logout();
        return redirect()->route('login')->with('logout', 'Anda telah Logout!');
    }

    public function index()
    {
        $user = User::all();
        return view('user.index', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //menampilkan layouting html pada folder resources-view
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:staff,guru',
        ]);

        $password = substr($request->email, 0, 3). substr($request->name, 0, 3);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($password),
        ]) ;
        //atau jika seluruh data input akan dimaukan langsung ke db bisa dengan perintah Medicine::create($request->all());
        return redirect()->route('user.index')->with('success', 'Berhasil menambahkan data User!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $medicine
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $medicine
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        //atau $medicine =  Medicine::where('id', $id)->first()
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $medicine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $user = User::find($id);

        $request->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:staff,guru',
        ]);

        if($request->password){
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'password' => Hash::make($request->password),
            ]);
        }else{
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ]);
        }

        return redirect()->route('user.index')->with('success', 'Berhasil mengubah data User!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $medicine
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        User::where('id', $id)->delete();

        return redirect()->route('user.index')->with('success', 'Berhasil menghapus data User!');
    }
}
