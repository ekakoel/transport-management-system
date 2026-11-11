<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
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

 public function showForgotPassword(){return view('auth.forgot-password');}

 public function sendResetLink(Request $request){
  $request->validate(['email'=>['required','email']]);
  $status=Password::sendResetLink($request->only('email'));
  if($status===Password::RESET_LINK_SENT){return back()->with('status','Link reset password telah dikirim ke email Anda.');}
  $message=$status===Password::RESET_THROTTLED?'Silakan tunggu beberapa saat sebelum meminta link baru.':'Email tidak terdaftar pada sistem.';
  return back()->withInput($request->only('email'))->withErrors(['email'=>$message]);
 }

 public function showResetPassword(Request $request,string $token){
  return view('auth.reset-password',['token'=>$token,'email'=>$request->string('email')->toString()]);
 }

 public function resetPassword(Request $request){
  $request->validate([
   'token'=>['required'],
   'email'=>['required','email'],
   'password'=>['required','string','min:8','confirmed'],
  ]);
  $status=Password::reset(
   $request->only('email','password','password_confirmation','token'),
   function(User $user,string $password){
    $user->forceFill(['password'=>$password])->setRememberToken(Str::random(60));
    $user->save();
    event(new PasswordReset($user));
   }
  );
  if($status===Password::PASSWORD_RESET){return redirect()->route('login')->with('status','Password berhasil diubah. Silakan login dengan password baru.');}
  return back()->withInput($request->only('email'))->withErrors(['email'=>'Link reset tidak valid atau sudah kedaluwarsa. Silakan minta link baru.']);
 }
}