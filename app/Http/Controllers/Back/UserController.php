<?php

namespace App\Http\Controllers\Back;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {

    }

    public function index()
    {
        $users = User::with('roles')->get();
        return view('back.pages.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        return view('back.pages.users.create', compact('roles'));
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => ['required', 'array'],
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            $roles = Role::whereIn('name', $request->roles)->pluck('name')->toArray();
            $user->syncRoles($request->roles);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Kullanıcı başarıyla oluşturuldu.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Bir hata oluştu: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();

        return view('back.pages.users.edit', compact('user', 'roles'));
    }


    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['nullable', 'string', 'max:20'],
            'roles' => ['required', 'array'],
        ];

        // Şifre alanı boş bırakılırsa doğrulama kurallarını ekleme
        if ($request->filled('password')) {
            $rules['password'] = ['confirmed', Rules\Password::defaults()];
        }

        $request->validate($rules);

        DB::beginTransaction();

        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Rolleri güncelle
            $user->syncRoles($request->roles);

            DB::commit();

            return redirect()->route('admin.users.index')
                ->with('success', 'Kullanıcı başarıyla güncellendi.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors(['error' => 'Bir hata oluştu: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Admin kullanıcısını silme kontrolü
        if ($user->hasRole('Admin') && User::role('Admin')->count() <= 1) {
            return back()->withErrors(['error' => 'Son Admin kullanıcısını silemezsiniz!']);
        }

        // Giriş yapan kullanıcının kendisini silme kontrolü
        if ($user->id == auth()->id()) {
            return back()->withErrors(['error' => 'Kendinizi silemezsiniz!']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }
}
