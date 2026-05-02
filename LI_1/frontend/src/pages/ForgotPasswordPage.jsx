import { useState } from 'react';
import api from '../api/axios';
import ValidationMessage from '../components/ValidationMessage';

export default function ForgotPasswordPage() {
  const [email, setEmail] = useState('');
  const [message, setMessage] = useState(null);
  const [error, setError] = useState(null);

  const handleSubmit = async (event) => {
    event.preventDefault();
    if (!email) {
      setError('Email is required.');
      return;
    }

    try {
      await api.post('/api/auth/forgot-password', { email });
      setMessage('If the account exists, a reset email has been sent.');
      setError(null);
    } catch {
      setError('Unable to send reset email.');
    }
  };

  return (
    <div className="max-w-md mx-auto bg-white p-6 rounded-xl shadow-sm">
      <h1 className="text-2xl font-semibold">Forgot Password</h1>
      <p className="text-slate-600 mt-2">Enter your email to receive password recovery instructions.</p>
      {message && <div className="text-green-600 mt-4">{message}</div>}
      <form className="space-y-4 mt-6" onSubmit={handleSubmit}>
        <div>
          <label className="block text-sm font-medium">Email</label>
          <input
            type="email"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
            className="mt-2 w-full"
          />
          <ValidationMessage message={error} />
        </div>
        <button type="submit" className="w-full bg-blue-600 text-white py-2 rounded-md">Send reset email</button>
      </form>
    </div>
  );
}
