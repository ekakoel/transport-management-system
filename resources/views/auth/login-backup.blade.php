@extends('layouts.app')

@section('title', 'Login | Bali Kami TMS')

@section('content')
<div class="tms-login-page">
    <div class="login-orb login-orb--one"></div>
    <div class="login-orb login-orb--two"></div>

    <main class="login-shell">
        {{-- LEFT: Brand and transport illustration --}}
        <section class="login-visual">
            <a href="{{ url('/') }}" class="brand">
                <span class="brand-mark">
                    <svg viewBox="0 0 48 48" fill="none" aria-hidden="true">
                        <path d="M7 30L16 12H25L16 30H7Z" fill="currentColor"/>
                        <path d="M20 30L29 12H38L29 30H20Z" fill="#39D6D0"/>
                        <path d="M13 35H35" stroke="currentColor"
                              stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="brand-copy">
                    <strong>BALI KAMI</strong>
                    <small>TRANSPORT MANAGEMENT</small>
                </span>
            </a>

            <div class="visual-content">
                <span class="eyebrow">
                    <span class="status-dot"></span>
                    TRANSPORT OPERATIONS PLATFORM
                </span>

                <h1>
                    Every journey.<br>
                    <span>Under control.</span>
                </h1>

                <p class="visual-description">
                    One intelligent workspace to coordinate drivers,
                    manage vehicles, monitor service operations, and keep
                    every journey moving.
                </p>

                <div class="route-illustration" aria-label="Illustration of a transport route">
                    <svg viewBox="0 0 520 245" fill="none"
                         xmlns="http://www.w3.org/2000/svg"
                         role="img" aria-hidden="true">
                        <defs>
                            <linearGradient id="routeGradient" x1="58" y1="180"
                                            x2="444" y2="65"
                                            gradientUnits="userSpaceOnUse">
                                <stop stop-color="#35D6CF"/>
                                <stop offset="1" stop-color="#FFAC67"/>
                            </linearGradient>
                            <linearGradient id="vehicleGradient" x1="0" y1="0"
                                            x2="1" y2="1">
                                <stop stop-color="#FFFFFF"/>
                                <stop offset="1" stop-color="#B9D4E8"/>
                            </linearGradient>
                        </defs>

                        <circle cx="260" cy="123" r="105"
                                stroke="#FFFFFF" stroke-opacity=".06"/>
                        <circle cx="260" cy="123" r="75"
                                stroke="#FFFFFF" stroke-opacity=".06"/>

                        <path d="M38 184C105 184 115 80 206 102
                                 S324 184 382 102 448 57 480 57"
                              stroke="#FFFFFF" stroke-opacity=".12"
                              stroke-width="2" stroke-dasharray="5 8"/>

                        <path d="M38 184C105 184 115 80 206 102
                                 S324 184 382 102 448 57 480 57"
                              stroke="url(#routeGradient)" stroke-width="3"
                              stroke-linecap="round" stroke-dasharray="7 9"
                              class="route-line"/>

                        <circle cx="38" cy="184" r="8" fill="#35D6CF"/>
                        <circle cx="38" cy="184" r="15"
                                stroke="#35D6CF" stroke-opacity=".2"/>
                        <circle cx="206" cy="102" r="7" fill="#35D6CF"/>
                        <circle cx="382" cy="102" r="7" fill="#FFAC67"/>
                        <circle cx="480" cy="57" r="8" fill="#FFAC67"/>
                        <circle cx="480" cy="57" r="15"
                                stroke="#FFAC67" stroke-opacity=".25"/>

                        <g transform="translate(174 124) rotate(-13)">
                            <rect x="0" y="0" width="94" height="52"
                                  rx="12" fill="#06182B"
                                  stroke="#FFFFFF" stroke-opacity=".13"/>
                            <rect x="8" y="7" width="78" height="34"
                                  rx="7" fill="url(#vehicleGradient)"/>
                            <path d="M19 13H38V35H19Z" fill="#0D314B"/>
                            <path d="M42 13H58V35H42Z" fill="#0D314B"/>
                            <path d="M62 13H76V35H62Z" fill="#0D314B"/>
                            <circle cx="22" cy="47" r="6" fill="#07111F"
                                    stroke="#7C9AB1" stroke-width="2"/>
                            <circle cx="72" cy="47" r="6" fill="#07111F"
                                    stroke="#7C9AB1" stroke-width="2"/>
                        </g>

                        <g transform="translate(318 28)">
                            <rect width="105" height="38" rx="10"
                                  fill="#FFFFFF" fill-opacity=".08"
                                  stroke="#FFFFFF" stroke-opacity=".12"/>
                            <circle cx="15" cy="19" r="4" fill="#35D6CF"/>
                            <text x="26" y="23" fill="#E9F5FF"
                                  font-size="10" font-family="Arial, sans-serif">
                                LIVE ROUTE
                            </text>
                        </g>

                        <g transform="translate(62 48)">
                            <rect width="96" height="35" rx="9"
                                  fill="#FFFFFF" fill-opacity=".07"
                                  stroke="#FFFFFF" stroke-opacity=".1"/>
                            <text x="12" y="22" fill="#B9D4E8"
                                  font-size="10" font-family="Arial, sans-serif">
                                DESTINATION
                            </text>
                        </g>
                    </svg>
                </div>

                <div class="visual-features">
                    <div class="feature-item">
                        <span class="feature-icon">
                            <svg viewBox="0 0 24 24" fill="none"
                                 aria-hidden="true">
                                <path d="M4 17V7l8-4 8 4v10l-8 4-8-4Z"
                                      stroke="currentColor"
                                      stroke-width="1.6"/>
                                <path d="M4 7l8 5 8-5M12 12v9"
                                      stroke="currentColor"
                                      stroke-width="1.6"/>
                            </svg>
                        </span>
                        <span><strong>Fleet Control</strong><small>Vehicles & drivers</small></span>
                    </div>

                    <div class="feature-item">
                        <span class="feature-icon feature-icon--orange">
                            <svg viewBox="0 0 24 24" fill="none"
                                 aria-hidden="true">
                                <circle cx="12" cy="12" r="9"
                                        stroke="currentColor"
                                        stroke-width="1.6"/>
                                <path d="M12 7v5l3 2"
                                      stroke="currentColor"
                                      stroke-width="1.6"
                                      stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span><strong>Service Tracking</strong><small>Every trip, organized</small></span>
                    </div>
                </div>
            </div>

            <div class="visual-footer">
                <span>BUILT FOR BETTER JOURNEYS</span>
                <span class="footer-line"></span>
                <span>BALI · INDONESIA</span>
            </div>
        </section>

        {{-- RIGHT: Login form --}}
        <section class="login-panel">
            <div class="mobile-brand">
                <span class="brand-mark">
                    <svg viewBox="0 0 48 48" fill="none" aria-hidden="true">
                        <path d="M7 30L16 12H25L16 30H7Z" fill="currentColor"/>
                        <path d="M20 30L29 12H38L29 30H20Z" fill="#39D6D0"/>
                        <path d="M13 35H35" stroke="currentColor"
                              stroke-width="3" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="brand-copy">
                    <strong>BALI KAMI</strong>
                    <small>TRANSPORT MANAGEMENT</small>
                </span>
            </div>

            <div class="login-form-wrap">
                <div class="login-heading">
                    <span class="welcome-label">WELCOME BACK</span>
                    <h2>Sign in to your<br>workspace<span>.</span></h2>
                    <p>Enter your credentials to access your operations.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert--success" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert--error" role="alert">
                        <strong>Unable to sign in.</strong>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('login.submit') }}"
                      class="login-form">
                    @csrf

                    <div class="field">
                        <label for="email">Email address</label>
                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24"
                                 fill="none" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14"
                                      rx="3" stroke="currentColor"
                                      stroke-width="1.6"/>
                                <path d="m4 7 8 6 8-6"
                                      stroke="currentColor"
                                      stroke-width="1.6"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@balikamitour.com"
                                autocomplete="username"
                                inputmode="email"
                                maxlength="190"
                                required
                                autofocus
                                class="@error('email') input-error @enderror">
                        </div>
                        @error('email')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="field">
                        <div class="label-row">
                            <label for="password">Password</label>
                        </div>

                        <div class="input-wrap">
                            <svg class="input-icon" viewBox="0 0 24 24"
                                 fill="none" aria-hidden="true">
                                <rect x="4" y="10" width="16" height="11"
                                      rx="3" stroke="currentColor"
                                      stroke-width="1.6"/>
                                <path d="M8 10V7a4 4 0 0 1 8 0v3"
                                      stroke="currentColor"
                                      stroke-width="1.6"
                                      stroke-linecap="round"/>
                                <circle cx="12" cy="15" r="1.3"
                                        fill="currentColor"/>
                            </svg>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                                class="@error('password') input-error @enderror">

                            <button class="password-toggle"
                                    type="button"
                                    aria-label="Show password"
                                    aria-controls="password"
                                    aria-pressed="false"
                                    data-password-toggle>
                                <svg class="eye-open" viewBox="0 0 24 24"
                                     fill="none" aria-hidden="true">
                                    <path d="M2.5 12s3.4-6 9.5-6 9.5 6 9.5 6-3.4 6-9.5 6-9.5-6-9.5-6Z"
                                          stroke="currentColor"
                                          stroke-width="1.6"/>
                                    <circle cx="12" cy="12" r="3"
                                            stroke="currentColor"
                                            stroke-width="1.6"/>
                                </svg>
                                <svg class="eye-closed" viewBox="0 0 24 24"
                                     fill="none" aria-hidden="true">
                                    <path d="m3 3 18 18M10.6 6.2A10.7 10.7 0 0 1 12 6c6.1 0 9.5 6 9.5 6a16 16 0 0 1-3.1 3.6M6.2 6.3C3.8 7.9 2.5 12 2.5 12s3.4 6 9.5 6c1.3 0 2.5-.3 3.5-.8"
                                          stroke="currentColor"
                                          stroke-width="1.6"
                                          stroke-linecap="round"/>
                                </svg>
                            </button>
                        </div>

                        @error('password')
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-options">
                        <label class="remember-option">
                            <input type="checkbox"
                                   name="remember"
                                   value="1"
                                   @checked(old('remember'))>
                            <span class="custom-checkbox"></span>
                            <span>Remember me</span>
                        </label>
                    </div>

                    <button type="submit" class="login-button">
                        <span>Sign in to dashboard</span>
                        <svg viewBox="0 0 24 24" fill="none"
                             aria-hidden="true">
                            <path d="M5 12h14m-6-6 6 6-6 6"
                                  stroke="currentColor"
                                  stroke-width="1.8"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                    </button>

                    <p class="secure-note">
                        <svg viewBox="0 0 24 24" fill="none"
                             aria-hidden="true">
                            <path d="M12 3 19 6v5c0 4.5-2.8 8-7 10-4.2-2-7-5.5-7-10V6l7-3Z"
                                  stroke="currentColor"
                                  stroke-width="1.6"
                                  stroke-linejoin="round"/>
                            <path d="m9 12 2 2 4-4"
                                  stroke="currentColor"
                                  stroke-width="1.6"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"/>
                        </svg>
                        Secure access to your operations
                    </p>
                </form>
            </div>

            <footer class="login-footer">
                <span>© {{ date('Y') }} Bali Kami TMS</span>
                <span>Transport operations, simplified.</span>
            </footer>
        </section>
    </main>
</div>
@endsection