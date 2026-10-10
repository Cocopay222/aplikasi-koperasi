import React from 'react';
import { useForm } from '@inertiajs/react';

export default function Login({ errors }) {
    const { data, setData, post, processing } = useForm({
        email: '',
        password: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <div style={{ padding: '50px', maxWidth: '400px', margin: '0 auto' }}>
            <h1>Login</h1>
            {errors.email && <div style={{ color: 'red' }}>{errors.email}</div>}
            
            <form onSubmit={submit}>
                <div style={{ marginBottom: '15px' }}>
                    <label>Email</label><br />
                    <input 
                        type="email" 
                        value={data.email} 
                        onChange={e => setData('email', e.target.value)} 
                        required 
                        style={{ width: '100%', padding: '8px' }}
                    />
                </div>
                
                <div style={{ marginBottom: '15px' }}>
                    <label>Password</label><br />
                    <input 
                        type="password" 
                        value={data.password} 
                        onChange={e => setData('password', e.target.value)} 
                        required 
                        style={{ width: '100%', padding: '8px' }}
                    />
                </div>
                
                <button type="submit" disabled={processing} style={{ padding: '10px 20px' }}>
                    {processing ? 'Loading...' : 'Login'}
                </button>
            </form>
        </div>
    );
}
