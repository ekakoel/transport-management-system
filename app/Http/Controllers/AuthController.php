<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
class AuthController extends Controller {
 public function show(){return view('auth.login');}
 public function login(Request $request){
  $credentials=$request->validate(['email'=>['required','email'],'password'=>['required','string']]);
  $credentials['is_active']=1;
  if(Auth::attempt($credentials,$request->boolean('remember'))){$request->session()->regenerate();return redirect()->intended(route('dashboard'));}
  return back()->withErrors(['email'=>'Email atau password tidak sesuai, atau akun tidak aktif.'])->onlyInput('email');
 }
 public function logout(Request $request){Auth::logout();$request->session()->invalidate();$request->session()->regenerateToken();return redirect()->route('login');}
 public function profile(Request $request){
  $user=$request->user();
  return view('profile',['user'=>$user,'reservationCount'=>$user->reservations()->count()]);
 }
 public function updateProfile(Request $request){
  $user=$request->user();
  $data=$request->validate([
   'name'=>['required','string','max:150'],
   'email'=>['required','email','max:190',Rule::unique('users','email')->ignore($user->id)],
   'phone'=>['nullable','string','max:40'],
   'current_password'=>['nullable','required_with:password','current_password'],
   'password'=>['nullable','string','min:8','confirmed'],
  ]);
  $user->name=$data['name'];
  $user->email=$data['email'];
  $user->phone=$data['phone'] ?? null;
  if(filled($data['password'] ?? null)){$user->password=$data['password'];}
  $user->save();
  return redirect()->route('profile')->with('success','Profil berhasil diperbarui.');
 }
}