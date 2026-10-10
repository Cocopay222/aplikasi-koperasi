import React, { useState, useEffect } from 'react';
import { useForm, Head } from '@inertiajs/react';

export default function Login({ errors }) {
    const { data, setData, post, processing } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const [showPassword, setShowPassword] = useState(false);
    const [isLoaded, setIsLoaded] = useState(false);

    useEffect(() => {
        // Simulate skeleton loading matching original blade
        const timer = setTimeout(() => {
            setIsLoaded(true);
        }, 800);
        return () => clearTimeout(timer);
    }, []);

    const submit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <div className="login-wrap">
            <Head title="Login - SAKTI Desk" />
            
            {/* Skeleton Loading State */}
            {!isLoaded && (
                <div className="content-wrap" id="skeleton-loading" style={{ width: '100%' }}>
                    <div className="login-split-layout">
                        {/* Left Column Skeleton */}
                        <div className="login-split-left">
                            <div className="brand">
                                <div className="skel" style={{ width: '38px', height: '38px', borderRadius: '8px', opacity: 0.2 }}></div>
                                <div className="brand-text">
                                    <div className="skel" style={{ width: '250px', height: '16px', marginBottom: '6px', opacity: 0.2, borderRadius: '4px' }}></div>
                                    <div className="skel" style={{ width: '200px', height: '10px', opacity: 0.2, borderRadius: '4px' }}></div>
                                </div>
                            </div>
                            
                            <div style={{ margin: 'auto 0' }}>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', marginBottom: '24px' }}>
                                    <div className="skel" style={{ width: '95%', maxWidth: '480px', height: '26px', opacity: 0.2, borderRadius: '6px' }}></div>
                                    <div className="skel" style={{ width: '90%', maxWidth: '480px', height: '26px', opacity: 0.2, borderRadius: '6px' }}></div>
                                    <div className="skel" style={{ width: '50%', maxWidth: '480px', height: '26px', opacity: 0.2, borderRadius: '6px' }}></div>
                                </div>
                                <div style={{ display: 'flex', flexDirection: 'column', gap: '10px' }}>
                                    {[98, 95, 92, 96, 94, 97, 85, 60].map((width, i) => (
                                        <div key={i} className="skel" style={{ width: `${width}%`, maxWidth: '480px', height: '12px', opacity: 0.2, borderRadius: '4px' }}></div>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Right Column Skeleton */}
                        <div className="login-split-right">
                            <div className="login-box" style={{ margin: '0 auto', maxWidth: '380px', width: '100%' }}>
                                <div className="login-panel-card" style={{ border: '1px solid var(--line)', padding: '28px 32px', borderRadius: '20px', background: '#fff' }}>
                                    <div className="skel" style={{ width: '140px', height: '12px', margin: '0 auto 8px auto', opacity: 0.1, borderRadius: '4px' }}></div>
                                    <div className="skel" style={{ width: '120px', height: '32px', margin: '0 auto 6px auto', opacity: 0.1, borderRadius: '8px' }}></div>
                                    <div className="skel" style={{ width: '300px', height: '12px', margin: '0 auto 20px auto', opacity: 0.1, borderRadius: '4px' }}></div>
                                    
                                    <div style={{ marginBottom: '14px' }}>
                                        <div className="skel" style={{ width: '50px', height: '12px', marginBottom: '6px', opacity: 0.1, borderRadius: '4px' }}></div>
                                        <div className="skel" style={{ width: '100%', height: '42px', opacity: 0.1, borderRadius: '8px' }}></div>
                                    </div>
                                    <div style={{ marginBottom: '18px' }}>
                                        <div className="skel" style={{ width: '80px', height: '12px', marginBottom: '6px', opacity: 0.1, borderRadius: '4px' }}></div>
                                        <div className="skel" style={{ width: '100%', height: '42px', opacity: 0.1, borderRadius: '8px' }}></div>
                                    </div>
                                    <div style={{ display: 'flex', justifyContent: 'space-between', marginBottom: '18px' }}>
                                        <div className="skel" style={{ width: '90px', height: '12px', opacity: 0.1, borderRadius: '4px' }}></div>
                                        <div className="skel" style={{ width: '110px', height: '12px', opacity: 0.1, borderRadius: '4px' }}></div>
                                    </div>
                                    <div className="skel" style={{ width: '100%', height: '44px', opacity: 0.1, borderRadius: '8px', marginBottom: '20px' }}></div>
                                    <div className="skel" style={{ width: '220px', height: '12px', margin: '0 auto', opacity: 0.1, borderRadius: '4px' }}></div>
                                </div>
                            </div>
                            <div className="skel" style={{ width: '200px', height: '14px', margin: '14px auto 0 auto', opacity: 0.1, borderRadius: '4px' }}></div>
                        </div>
                    </div>
                </div>
            )}

            {/* Actual Content */}
            {isLoaded && (
                <div className="content-wrap loaded" id="actual-content" style={{ width: '100%', display: 'flex' }}>
                    <div className="login-split-layout fade-up" style={{ animationDelay: '0.1s' }}>
                        {/* Left Column */}
                        <div className="login-split-left">
                            <div className="brand">
                                <img src="/logo.png" alt="Logo" />
                                <div className="brand-text">
                                    <h2>PT SAKTI Kinerja Kolaborasindo</h2>
                                    <p>Portal Layanan &amp; Dukungan Koperasi</p>
                                </div>
                            </div>
                            
                            <div className="login-text-content" style={{ position: 'relative', zIndex: 10 }}>
                                <p style={{ color: 'rgba(255, 255, 255, 0.8)', marginBottom: '8px', fontSize: 'calc(12px * var(--text-scale, 1))', fontWeight: 700, letterSpacing: '0.1em', textTransform: 'uppercase' }}>PORTAL LAYANAN PENGGUNA</p>
                                <h1 style={{ marginTop: 0 }}>Portal Layanan Pengguna Aplikasi SAKTI</h1>
                                <p className="desc" style={{ marginBottom: 0 }}>
                                    Dapatkan dukungan dan informasi yang Anda butuhkan dalam menggunakan Aplikasi SAKTI. Melalui portal ini, pengguna dapat menyampaikan kendala, memperoleh bantuan layanan, serta mengakses informasi dan pembaruan secara lebih mudah dan terpusat.
                                </p>
                                <p className="desc-mobile" style={{ display: 'none' }}>Pantau status, riwayat, dan estimasi penyelesaian tiket anda langsung dari dashboard.</p>
                            </div>

                            {/* Footer Info */}
                            <div style={{ marginTop: 'auto', position: 'relative', zIndex: 10, fontSize: '11px', color: 'rgba(255, 255, 255, 0.5)', fontFamily: 'var(--font-mono)', display: 'flex', alignItems: 'center', gap: '10px', paddingTop: '24px' }}>
                                <span>&copy; PT SAKTI Kinerja Kolaborasindo</span>
                                <span>&bull;</span>
                                <span>Portal Pelaporan Mitra</span>
                            </div>
                        </div>

                        {/* Right Column */}
                        <div className="login-split-right">
                            <div className="login-box fade-up" style={{ animationDelay: '0.15s', margin: '0 auto', maxWidth: '380px', width: '100%' }}>
                                <div className="login-panel-card fade-up" style={{ animationDelay: '0.2s' }}>
                                    <p className="eyebrow" style={{ color: 'var(--amber)', marginBottom: '6px', fontSize: 'calc(11px * var(--text-scale, 1))' }}>Masuk ke Akun</p>
                                    <h1 style={{ marginBottom: '4px', fontSize: 'calc(22px * var(--text-scale, 1))' }}>Login</h1>
                                    <p className="lede" style={{ marginBottom: '18px', fontSize: 'calc(12px * var(--text-scale, 1))' }}>Masukkan email dan kata sandi Anda untuk melanjutkan</p>

                                    {/* Error Message */}
                                    {Object.keys(errors).length > 0 && (
                                        <div id="login-error" className="alert-dismiss" style={{ textAlign: 'left', marginBottom: '14px', fontWeight: 500, color: 'var(--amber)', background: 'var(--amber-soft)', padding: '10px 12px', borderRadius: '8px', display: 'flex', justifyContent: 'space-between', alignItems: 'flex-start', transition: 'opacity 0.6s ease, transform 0.6s ease' }}>
                                            <div>
                                                {Object.values(errors).map((error, idx) => (
                                                    <div key={idx}>{error}</div>
                                                ))}
                                            </div>
                                            <button type="button" onClick={(e) => e.currentTarget.parentNode.style.display = 'none'} style={{ background: 'none', border: 'none', color: 'var(--amber)', cursor: 'pointer', fontSize: 'calc(18px * var(--text-scale, 1))', fontWeight: 'bold', lineHeight: 1, padding: '0 4px', marginLeft: '10px' }}>&times;</button>
                                        </div>
                                    )}
                                    
                                    <form onSubmit={submit}>
                                        <div className="field">
                                            <label style={{ fontSize: 'calc(12px * var(--text-scale, 1))' }}>Email</label>
                                            <div style={{ position: 'relative', display: 'flex', alignItems: 'center' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ position: 'absolute', left: '14px', top: '50%', transform: 'translateY(-50%)', pointerEvents: 'none', opacity: 0.7 }}>
                                                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
                                                    <polyline points="22,6 12,13 2,6"></polyline>
                                                </svg>
                                                <input 
                                                    type="email" 
                                                    name="email" 
                                                    placeholder="support@skk.co.id" 
                                                    style={{ paddingLeft: '42px', fontSize: 'calc(13px * var(--text-scale, 1))' }} 
                                                    required 
                                                    value={data.email}
                                                    onChange={e => setData('email', e.target.value)}
                                                />
                                            </div>
                                        </div>
                                        <div className="field">
                                            <label style={{ fontSize: 'calc(12px * var(--text-scale, 1))' }}>Password</label>
                                            <div style={{ position: 'relative', display: 'flex', alignItems: 'center' }}>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ position: 'absolute', left: '14px', top: '50%', transform: 'translateY(-50%)', pointerEvents: 'none', opacity: 0.7 }}>
                                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                                </svg>
                                                <input 
                                                    type={showPassword ? 'text' : 'password'} 
                                                    name="password" 
                                                    placeholder="••••••••" 
                                                    style={{ paddingLeft: '42px', paddingRight: '42px', fontSize: 'calc(13px * var(--text-scale, 1))', width: '100%', boxSizing: 'border-box' }} 
                                                    required 
                                                    value={data.password}
                                                    onChange={e => setData('password', e.target.value)}
                                                />
                                                <button 
                                                    type="button" 
                                                    onClick={() => setShowPassword(!showPassword)}
                                                    style={{ position: 'absolute', right: '14px', top: '50%', transform: 'translateY(-50%)', background: 'none', border: 'none', padding: 0, cursor: 'pointer', color: '#64748b', display: 'flex', alignItems: 'center', justifyContent: 'center', opacity: 0.7, transition: 'opacity 0.2s' }}
                                                    onMouseOver={e => e.currentTarget.style.opacity = '1'} 
                                                    onMouseOut={e => e.currentTarget.style.opacity = '0.7'}
                                                >
                                                    {showPassword ? (
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                            <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/>
                                                            <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/>
                                                            <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/>
                                                            <line x1="2" x2="22" y1="2" y2="22"/>
                                                        </svg>
                                                    ) : (
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                                            <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                                                            <circle cx="12" cy="12" r="3"/>
                                                        </svg>
                                                    )}
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '18px', fontSize: 'calc(11px * var(--text-scale, 1))' }}>
                                            <label style={{ display: 'flex', alignItems: 'center', gap: '6px', cursor: 'pointer', color: 'var(--ink-soft)', fontWeight: 500 }}>
                                                <input 
                                                    type="checkbox" 
                                                    name="remember" 
                                                    checked={data.remember}
                                                    onChange={e => setData('remember', e.target.checked)}
                                                    style={{ width: '14px', height: '14px', accentColor: 'var(--brand-primary)', margin: 0, padding: 0, border: '1.5px solid var(--line)', borderRadius: '4px', cursor: 'pointer' }} 
                                                />
                                                Ingat saya
                                            </label>
                                            <a href="#" style={{ color: 'var(--brand-primary)', fontWeight: 600, textDecoration: 'underline' }}>Lupa kata sandi?</a>
                                        </div>

                                        <button 
                                            type="submit" 
                                            className="btn" 
                                            disabled={processing}
                                            style={{ width: '100%', justifyContent: 'center', background: '#17447e', border: 'none', color: 'white', padding: '11px 14px', borderRadius: '10px', fontSize: '13px', fontWeight: 600, boxShadow: '0 4px 14px rgba(23, 68, 126, 0.35)', transition: 'all 0.2s', cursor: processing ? 'not-allowed' : 'pointer', opacity: processing ? 0.7 : 1 }}
                                            onMouseOver={e => !processing && (e.currentTarget.style.transform = 'translateY(-2px)', e.currentTarget.style.background = '#123566', e.currentTarget.style.boxShadow = '0 6px 20px rgba(23, 68, 126, 0.45)')} 
                                            onMouseOut={e => !processing && (e.currentTarget.style.transform = 'translateY(0)', e.currentTarget.style.background = '#17447e', e.currentTarget.style.boxShadow = '0 4px 14px rgba(23, 68, 126, 0.35)')}
                                        >
                                            {processing ? 'Loading...' : 'Masuk ke Dashboard →'}
                                        </button>
                                    </form>

                                    <div className="register-prompt" style={{ position: 'relative', marginTop: '20px', borderTop: 'none', paddingTop: '18px', fontSize: 'calc(11px * var(--text-scale, 1))' }}>
                                        <div style={{ position: 'absolute', top: 0, left: '50%', transform: 'translate(-50%, -50%)', background: 'var(--paper-raised)', padding: '0 10px', fontSize: 'calc(9px * var(--text-scale, 1))', color: 'var(--ink-soft)', fontFamily: 'var(--font-mono)', zIndex: 2 }}>atau</div>
                                        <div style={{ position: 'absolute', top: 0, left: 0, right: 0, borderTop: '1px solid var(--line)', zIndex: 1 }}></div>
                                        Belum mempunyai akun? <a href="#" style={{ color: 'var(--brand-primary)', fontWeight: 700, textDecoration: 'underline' }}>Daftar</a>
                                    </div>
                                </div>
                            </div>

                            <div className="bottom-text fade-up" style={{ animationDelay: '0.25s' }}>
                                Butuh bantuan masuk? <a href="#">Hubungi kami</a>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
